<?php

namespace App\Exports\Producer;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $producerId = Auth::id();

        return Product::where('user_id', $producerId)
            ->with('category')
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('sum(quantity)'));
            }])
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Producto',
            'Nombre',
            'Categoría',
            'Precio',
            'Stock Actual',
            'Total Vendido (unidades)',
            'Estado',
        ];
    }

    public function map($product): array
    {
        $stock = $product->stock_quantity ?? 0;
        $status = 'Disponible';
        if ($stock <= 0) {
            $status = 'Agotado';
        } elseif ($stock <= 10) {
            $status = 'Stock Bajo';
        }

        return [
            $product->id,
            $product->name,
            $product->category ? $product->category->name : 'N/A',
            $product->price,
            $stock,
            $product->total_sold ?? 0,
            $status,
        ];
    }
}
