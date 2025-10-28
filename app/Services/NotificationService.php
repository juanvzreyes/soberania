<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use App\Mail\DeliveryPendingMail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendOrderConfirmation(Order $order): void
    {
        $order->load(['orderItems.product', 'payment', 'delivery']);
        $user = $order->consumer_id ? $order->consumer : $order->cooperative;

        if (!$user) {
            return;
        }
        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ORDER_CONFIRMATION,
            'title' => '¡Pedido confirmado!',
            'message' => "Tu pedido #{$order->id} ha sido confirmado por un total de \${$order->total_amount}.",
            'data' => [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment->payment_method ?? null,
            ],
        ]);
        try {
            Mail::to($user->email)->send(new OrderConfirmationMail($order, $user));
        } catch (\Exception $e) {
            Log::error('Error enviando email de confirmación: ' . $e->getMessage());
        }
    }

    public function sendDeliveryPendingReminder(Order $order): void
    {
        $order->load(['delivery.transporter', 'orderItems.product']);
        $user = $order->consumer_id ? $order->consumer : $order->cooperative;

        if (!$user) {
            return;
        }
        $delivery = $order->delivery;
        $transporterName = $delivery->transporter ? $delivery->transporter->name : 'Por asignar';
        $estimatedDate = $delivery->estimated_delivery_date 
            ? $delivery->estimated_delivery_date->format('d/m/Y H:i') 
            : 'Por confirmar';
        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_DELIVERY_PENDING,
            'title' => 'Entrega programada',
            'message' => "Tu pedido #{$order->id} será entregado el {$estimatedDate}. Transportista: {$transporterName}",
            'data' => [
                'order_id' => $order->id,
                'delivery_id' => $delivery->id,
                'transporter_name' => $transporterName,
                'estimated_date' => $delivery->estimated_delivery_date,
            ],
        ]);

        try {
            Mail::to($user->email)->send(new DeliveryPendingMail($order, $user));
        } catch (\Exception $e) {
            Log::error('Error enviando email de entrega pendiente: ' . $e->getMessage());
        }
    }
}