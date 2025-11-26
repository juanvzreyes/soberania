<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use App\Mail\DeliveryPendingMail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderDeliveredMail;
use App\Mail\LowStockAlertMail;
use App\Models\Product;
use App\Mail\NewProductMail;
use App\Mail\NewCategoryMail;
use App\Models\Category;

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
                'payment_method' => isset($order->payment) ? $order->payment->payment_method : null,
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

    public function sendOrderDelivered(Order $order): void
    {
        $order->load(['orderItems.product', 'payment', 'delivery.transporter']);
        $consumer = $order->consumer_id ? $order->consumer : $order->cooperative;

        if ($consumer) {
            Notification::create([
                'user_id' => $consumer->id,
                'type' => Notification::TYPE_DELIVERY_COMPLETED,
                'title' => '¡Pedido entregado!',
                'message' => "Tu pedido #{$order->id} ha sido entregado exitosamente.",
                'data' => [
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'delivered_at' => now()->toDateTimeString(),
                ],
            ]);

            try {
                Mail::to($consumer->email)->send(new OrderDeliveredMail($order, $consumer));
            } catch (\Exception $e) {
                Log::error('Error enviando email de entrega completada al consumidor: ' . $e->getMessage());
            }
        }
        if ($order->cooperative_id && $order->consumer_id) {
            $cooperative = $order->cooperative;

            Notification::create([
                'user_id' => $cooperative->id,
                'type' => Notification::TYPE_DELIVERY_COMPLETED,
                'title' => 'Pedido entregado',
                'message' => "El pedido #{$order->id} para {$consumer->name} ha sido entregado exitosamente.",
                'data' => [
                    'order_id' => $order->id,
                    'consumer_name' => $consumer->name,
                    'total_amount' => $order->total_amount,
                    'delivered_at' => now()->toDateTimeString(),
                ],
            ]);

            try {
                Mail::to($cooperative->email)->send(new OrderDeliveredMail($order, $cooperative));
            } catch (\Exception $e) {
                Log::error('Error enviando email de entrega completada a cooperativa: ' . $e->getMessage());
            }
        }
    }

    public function sendLowStockAlert(Product $product, User $producer): void
    {
        if (!$product->relationLoaded('category')) {
            $product->load('category');
        }
        Notification::create([
            'user_id' => $producer->id,
            'type' => Notification::TYPE_LOW_STOCK,
            'title' => 'Stock bajo',
            'message' => "El producto '{$product->name}' tiene solo {$product->stock_quantity} unidades disponibles. Se recomienda reabastecer.",
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock_quantity' => $product->stock_quantity,
                'category' => isset($product->category) ? $product->category->name : 'Sin categoría',
            ],
        ]);

        try {
            Mail::to($producer->email)->send(new LowStockAlertMail($product, $producer));
        } catch (\Exception $e) {
            Log::error('Error enviando email de stock bajo: ' . $e->getMessage());
        }
    }

    public function sendNewProductNotification(Product $product): void
    {
        $product->load(['category', 'photos', 'user']);
        if ($product->user) {
            Notification::create([
                'user_id' => $product->user->id,
                'type' => Notification::TYPE_NEW_PRODUCT,
                'title' => 'Producto publicado',
                'message' => "Tu producto '{$product->name}' ha sido publicado exitosamente.",
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'category' => isset($product->category) ? $product->category->name : 'Sin categoría',
                    'price' => $product->price,
                ],
            ]);

            try {
                Mail::to($product->user->email)->send(new NewProductMail($product, $product->user));
            } catch (\Exception $e) {
                Log::error('Error enviando email de nuevo producto al productor: ' . $e->getMessage());
            }
        }
        $consumers = User::where('role', 'consumer')
            ->whereNotNull('email')
            ->get();

        foreach ($consumers as $consumer) {
            Notification::create([
                'user_id' => $consumer->id,
                'type' => Notification::TYPE_NEW_PRODUCT,
                'title' => '🆕 Nuevo producto disponible',
                'message' => "Se ha agregado '{$product->name}' al catálogo en la categoría " . (isset($product->category) ? $product->category->name : 'Sin categoría') . ".",
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'category' => isset($product->category) ? $product->category->name : 'Sin categoría',
                    'price' => $product->price,
                ],
            ]);
            if ($consumers->search($consumer) < 50) {
                try {
                    Mail::to($consumer->email)->send(new NewProductMail($product, $consumer));
                } catch (\Exception $e) {
                    Log::error("Error enviando email de nuevo producto a {$consumer->email}: " . $e->getMessage());
                }
            }
        }
    }
}
