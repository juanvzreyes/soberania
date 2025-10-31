<?php

namespace App\Exports\Producer;

use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    // protected $location;

    public function __construct($startDate, $endDate, $location = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        // $this->location = $location;
    }

    public function collection()
    {
        $producerId = Auth::id();

        $itemsSold = OrderItem::query()
            ->whereHas('product', function ($query) use ($producerId) {
                $query->where('user_id', $producerId);
            })
            ->whereHas('order', function ($query) {
                $query->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));
            })
            ->with('order:id,consumer_id,cooperative_id')
            ->get();

        $spendingByCustomer = $itemsSold->groupBy(function ($item) {
            return $item->order->consumer_id ?? $item->order->cooperative_id;
        })->map(function ($customerItems) {
            return $customerItems->sum(fn($item) => $item->quantity * $item->price);
        })->filter();

        if ($spendingByCustomer->isEmpty()) {
            return collect();
        }

        $allCustomerIds = $spendingByCustomer->sortDesc()->keys();

        $allCustomers = User::find($allCustomerIds)
            ->map(function ($customer) use ($spendingByCustomer) {
                $customer->total_spent = $spendingByCustomer->get($customer->id);
                return $customer;
            })
            ->sortByDesc('total_spent')->values();
        return $allCustomers;
    }

    public function headings(): array
    {
        return [
            'ID Cliente',
            'Nombre',
            'Email',
            'Total Gastado',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->total_spent ?? 0,
        ];
    }
}
