<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProducersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'Producer');
        })
            ->with(['products', 'producer.location'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre del Productor',
            'Email',
            'Calle',
            'Número Exterior',
            'Código Postal',
            'Productos',
            'Total de Productos',
        ];
    }

    public function map($producer): array
    {
        return [
            $producer->id,
            $producer->name,
            $producer->email,
            $producer->producer->location->street ?? 'N/A',
            $producer->producer->location->exterior_number ?? 'N/A',
            $producer->producer->location->postal_code ?? 'N/A',
            $producer->products->isNotEmpty() ? $producer->products->pluck('name')->implode(', ') : 'Sin productos',
            $producer->products->count(),
        ];
    }
}
