<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CooperativeOrdersExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    protected $orders;
    protected $stats;
    protected $startDate;
    protected $endDate;

    public function __construct($orders, $stats, $startDate, $endDate)
    {
        $this->orders = $orders;
        $this->stats = $stats;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection(): Collection
    {
        $data = collect();

        // Agregar información del reporte
        $data->push(['REPORTE DE PEDIDOS - COOPERATIVA']);
        $data->push(['Periodo:', Carbon::parse($this->startDate)->format('d/m/Y'), 'al', Carbon::parse($this->endDate)->format('d/m/Y')]);
        $data->push(['Generado:', Carbon::now()->format('d/m/Y H:i:s')]);
        $data->push([]);
        
        // Estadísticas
        $data->push(['RESUMEN GENERAL']);
        $data->push(['Total de Pedidos:', $this->stats['total_orders']]);
        $data->push(['Total de Productos:', $this->stats['total_products']]);
        $data->push(['Ingresos Totales:', '$' . number_format($this->stats['total_revenue'], 2)]);
        $data->push(['Pedidos Pendientes:', $this->stats['pending_orders']]);
        $data->push(['Pedidos Completados:', $this->stats['completed_orders']]);
        $data->push([]);
        $data->push([]);

        // Encabezados de la tabla
        $data->push(['Número de Orden', 'Fecha', 'Cliente', 'Producto', 'Categoría', 'Cantidad', 'Precio Unitario', 'Subtotal', 'Estado']);

        // Datos de pedidos
        foreach ($this->orders as $order) {
            $data->push([
                $order->order_number,
                Carbon::parse($order->order_date)->format('d/m/Y H:i'),
                $order->consumer_name,
                $order->product_name,
                $order->category_name ?? 'Sin categoría',
                $order->quantity,
                '$' . number_format($order->price, 2),
                '$' . number_format($order->subtotal, 2),
                $order->status,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            ],
            13 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Pedidos Cooperativa';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = count($this->orders) + 13;
                $event->sheet->getStyle('A13:I' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('F14:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('G14:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}