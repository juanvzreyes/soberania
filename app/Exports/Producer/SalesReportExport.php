<?php

namespace App\Exports\Producer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $producerId = Auth::id();

        return Order::whereHas('orderItems.product', fn($q) => $q->where('user_id', $producerId))
            ->with([
                'consumer',
                'cooperative',
                'orderItems' => function ($query) use ($producerId) {
                    $query->whereHas('product', fn($q2) => $q2->where('user_id', $producerId))
                        ->with(['product' => fn($q3) => $q3->withTrashed()]);
                },
            ])
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->get();
    }

    public function headings(): array
    {
        return ['ID Pedido', 'Fecha', 'Cliente', 'Productos (del Productor)', 'Total (del Productor)'];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->created_at->format('Y-m-d'),

            ($order->consumer?->name ?? $order->cooperative?->name) ?? 'N/A',
            $order->orderItems->map(fn($item) => $item->product?->name ?? 'Producto no disponible')->implode(', '),
            $order->orderItems->sum(fn($item) => $item->quantity * $item->price),
        ];
    }
}
