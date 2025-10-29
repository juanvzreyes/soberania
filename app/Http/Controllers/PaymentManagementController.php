<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Inertia\Response;
use Exception;
use App\Traits\Filterable;

class PaymentManagementController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'Payments/Pages/';
        $this->model = new Payment();
        $this->routeName = 'payments.';
        
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}show")->only(['show']);
        $this->middleware("permission:{$this->routeName}updateStatus")->only(['updateStatus']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $status = $request->get('status');
        $method = $request->get('method');

        $query = Payment::with(['order.consumer', 'order.cooperative', 'order.orderItems.product'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($method) {
            $query->where('payment_method', $method);
        }

        $payments = $query->paginate($filters->rows)->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'payments' => $payments,
            'title' => 'Gestión de Pagos',
            'routeName' => $this->routeName,
            'filters' => [
                'status' => $status,
                'method' => $method,
            ],
            'statusOptions' => Payment::getStatusOptions(),
            'methodOptions' => Payment::getMethodOptions(),
        ]);
    }
    
    public function show(Payment $payment): Response
    {
        $payment->load(['order.consumer', 'order.orderItems.product', 'order.cooperative', 'order.delivery']);

        return Inertia::render("{$this->source}Show", [
            'payment' => $payment,
            'title' => 'Detalle de Pago #' . $payment->id,
            'routeName' => $this->routeName,
            'statusOptions' => Payment::getStatusOptions(),
        ]);
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', array_keys(Payment::getStatusOptions())),
            ]);

            $newStatus = $validated['status'];
            $oldStatus = $payment->status;

            $this->validatePaymentStatusChange($payment, $newStatus);

            DB::beginTransaction();

            $payment->update(['status' => $newStatus]);

            DB::commit();

            \App\Models\Notification::create([
                'user_id' => $payment->order->consumer_id ?? $payment->order->cooperative_id,
                'title' => 'Actualización de Pago',
                'message' => "El pago del pedido #{$payment->order_id} cambió de estado: {$oldStatus} → {$newStatus}",
                'type' => 'payment_status_update',
                'data' => [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                ],
            ]);

            return back()->with('success', 'Estado del pago actualizado correctamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar estado del pago ID {$payment->id}: " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    private function validatePaymentStatusChange(Payment $payment, string $newStatus): void
    {
        $order = $payment->order;
        if ($newStatus === Payment::STATUS_CONFIRMED && $order->status === Order::STATUS_CANCELED) {
            throw new Exception('No se puede confirmar un pago de un pedido cancelado.');
        }
        if ($newStatus === Payment::STATUS_REFUNDED && $order->status === Order::STATUS_DELIVERED) {
            throw new Exception('No se puede revertir un pago de un pedido ya entregado.');
        }
    }
}