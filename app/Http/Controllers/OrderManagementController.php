<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'Orders/Pages/';
        $this->model = new Order();
        $this->routeName = 'orders.';
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}show")->only(['show']);
        $this->middleware("permission:{$this->routeName}updateStatus")->only(['updateStatus']);
        $this->middleware("permission:{$this->routeName}cancel")->only(['cancel']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Order::with(['consumer', 'cooperative', 'orderItems.product', 'payment', 'delivery']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'orders' => $orders,
            'filters' => [
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'statusOptions' => Order::getStatusOptions(),
            'title' => 'Gestión de Pedidos',
            'routeName' => $this->routeName,
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load([
            'consumer',
            'cooperative',
            'orderItems.product.photos',
            'payment',
            'delivery.transporter'
        ]);

        return Inertia::render("{$this->source}Show", [
            'order' => $order,
            'canUpdateStatus' => true,
            'statusOptions' => Order::getStatusOptions(),
            'title' => 'Detalle del Pedido #' . $order->id,
            'routeName' => $this->routeName,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', array_keys(Order::getStatusOptions())),
                'notes' => 'nullable|string|max:500',
            ]);

            $newStatus = $validated['status'];
            $oldStatus = $order->status;
            $this->validateStatusChange($order, $newStatus);

            DB::beginTransaction();
            $order->update(['status' => $newStatus]);
            $this->syncRelatedStatuses($order, $newStatus);

            DB::commit();
            \App\Models\Notification::create([
                'user_id' => $order->consumer_id ?? $order->cooperative_id,
                'title' => 'Actualización de Pedido',
                'message' => "Tu pedido #{$order->id} cambió de estado: {$oldStatus} → {$newStatus}",
                'type' => 'order_status_update',
                'data' => [
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ],
            ]);

            return back()->with('success', 'Estado del pedido actualizado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error al actualizar estado del pedido ID {$order->id}: " . $exception->getMessage());
            return back()->with('error', $exception->getMessage());
        }
    }

    public function cancel(Request $request, Order $order)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            DB::beginTransaction();

            $order->update([
                'status' => Order::STATUS_CANCELED,
                'cancellation_reason' => $request->reason,
                'cancelled_at' => now(),
            ]);
            $order->delivery?->update(['status' => Delivery::STATUS_CANCELED]);
            $order->payment?->update(['status' => Payment::STATUS_CANCELED]);
            foreach ($order->orderItems as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
                if ($item->product->stock_quantity > 0) {
                    $item->product->update(['is_available' => true]);
                }
            }

            DB::commit();
            \App\Models\Notification::create([
                'user_id' => $order->consumer_id ?? $order->cooperative_id,
                'title' => 'Pedido Cancelado',
                'message' => "Tu pedido #{$order->id} ha sido cancelado. Razón: {$request->reason}",
                'type' => 'order_cancelled',
                'data' => [
                    'order_id' => $order->id,
                    'reason' => $request->reason,
                ],
            ]);

            return redirect()->route("{$this->routeName}index")
                ->with('success', 'Pedido cancelado correctamente');

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error al cancelar pedido ID {$order->id}: " . $exception->getMessage());
            return back()->with('error', 'Error al cancelar el pedido.');
        }
    }

    private function validateStatusChange(Order $order, string $newStatus): void
    {
        $payment = $order->payment;
        $delivery = $order->delivery;

        switch ($newStatus) {
            case Order::STATUS_PROCESSING:
                if (!$payment || $payment->status !== Payment::STATUS_CONFIRMED) {
                    throw new \Exception('No se puede cambiar a "En preparación". El pago debe estar confirmado primero.');
                }
                break;

            case Order::STATUS_SHIPPING:
                if (!$payment || $payment->status !== Payment::STATUS_CONFIRMED) {
                    throw new \Exception('No se puede cambiar a "En camino". El pago debe estar confirmado primero.');
                }
                if (!$delivery || $delivery->status !== Delivery::STATUS_ON_THE_WAY) {
                    throw new \Exception('No se puede cambiar a "En camino". La entrega debe estar marcada como "En camino" primero.');
                }
                break;

            case Order::STATUS_DELIVERED:
                if (!$payment || $payment->status !== Payment::STATUS_CONFIRMED) {
                    throw new \Exception('No se puede marcar como entregado. El pago debe estar confirmado primero.');
                }
                if (!$delivery || $delivery->status !== Delivery::STATUS_DELIVERED) {
                    throw new \Exception('No se puede marcar como entregado. La entrega debe estar marcada como "Entregado" primero.');
                }
                break;

            case Order::STATUS_CANCELED:
                break;
        }
    }
    private function syncRelatedStatuses(Order $order, string $newStatus): void
    {
        $delivery = $order->delivery;

        switch ($newStatus) {
            case Order::STATUS_PROCESSING:
                $delivery?->update(['status' => Delivery::STATUS_IN_PREPARATION]);
                break;
            case Order::STATUS_CANCELED:
                $delivery?->update(['status' => Delivery::STATUS_CANCELED]);
                $order->payment?->update(['status' => Payment::STATUS_CANCELED]);
                foreach ($order->orderItems as $item) {
                    $item->product->increment('stock_quantity', $item->quantity);
                    if ($item->product->stock_quantity > 0) {
                        $item->product->update(['is_available' => true]);
                    }
                }
                break;
        }
    }
}