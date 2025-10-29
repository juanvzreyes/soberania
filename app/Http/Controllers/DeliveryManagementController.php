<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Inertia\Response;
use Exception;
use App\Traits\Filterable;

class DeliveryManagementController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'Deliveries/Pages/';
        $this->model = new Delivery();
        $this->routeName = 'deliveries.';
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}show")->only(['show']);
        $this->middleware("permission:{$this->routeName}updateStatus")->only(['updateStatus']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $status = $request->get('status');

        $query = Delivery::with(['order.consumer', 'order.cooperative', 'order.orderItems.product'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $deliveries = $query->paginate($filters->rows)->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'deliveries' => $deliveries,
            'title' => 'Gestión de Entregas',
            'routeName' => $this->routeName,
            'filters' => ['status' => $status],
            'statusOptions' => Delivery::getStatusOptions(),
        ]);
    }

    public function show(Delivery $delivery): Response
    {
        $delivery->load([
            'order.consumer',
            'order.cooperative',
            'order.orderItems.product',
            'order.payment'
        ]);

        return Inertia::render("{$this->source}Show", [
            'delivery' => $delivery,
            'title' => 'Detalle de Entrega #' . $delivery->id,
            'routeName' => $this->routeName,
            'statusOptions' => Delivery::getStatusOptions(),
        ]);
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', array_keys(Delivery::getStatusOptions())),
            ]);

            $newStatus = $validated['status'];
            $oldStatus = $delivery->status;
            $this->validateDeliveryStatusChange($delivery, $newStatus);

            DB::beginTransaction();

            $delivery->update(['status' => $newStatus]);

            DB::commit();
            \App\Models\Notification::create([
                'user_id' => $delivery->order->consumer_id ?? $delivery->order->cooperative_id,
                'title' => 'Actualización de Entrega',
                'message' => "La entrega del pedido #{$delivery->order_id} cambió de estado: {$oldStatus} → {$newStatus}",
                'type' => 'delivery_status_update',
                'data' => [
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                ],
            ]);

            return back()->with('success', 'Estado de la entrega actualizado correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar estado de la entrega ID {$delivery->id}: " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    private function validateDeliveryStatusChange(Delivery $delivery, string $newStatus): void
    {
        $order = $delivery->order;
        if ($newStatus === Delivery::STATUS_DELIVERED && $order->status !== Order::STATUS_SHIPPING) {
            throw new Exception('No se puede marcar como entregado. El pedido debe estar "En camino" primero.');
        }
        if ($order->status === Order::STATUS_CANCELED && $newStatus !== Delivery::STATUS_CANCELED) {
            throw new Exception('No se puede cambiar el estado de una entrega de un pedido cancelado.');
        }
    }
}
