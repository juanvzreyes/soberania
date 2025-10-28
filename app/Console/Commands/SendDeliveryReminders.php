<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Delivery;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendDeliveryReminders extends Command
{
    protected $signature = 'notifications:delivery-reminders';
    protected $description = 'Enviar recordatorios de entregas pendientes';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Buscando entregas pendientes...');
        $tomorrow = Carbon::tomorrow();
        $in24Hours = Carbon::now()->addHours(24);

        $deliveries = Delivery::whereBetween('estimated_delivery_date', [$tomorrow->startOfDay(), $tomorrow->endOfDay()])
            ->whereIn('status', [Delivery::STATUS_PENDING_ASSIGNMENT, Delivery::STATUS_IN_PREPARATION])
            ->with(['order.consumer', 'order.cooperative'])
            ->get();

        $count = 0;

        foreach ($deliveries as $delivery) {
            try {
                $this->notificationService->sendDeliveryPendingReminder($delivery->order);
                $count++;
                $this->info("Recordatorio enviado para pedido #{$delivery->order->id}");
            } catch (\Exception $e) {
                $this->error("Error en pedido #{$delivery->order->id}: " . $e->getMessage());
            }
        }

        $this->info("Total de recordatorios enviados: {$count}");
    }
}