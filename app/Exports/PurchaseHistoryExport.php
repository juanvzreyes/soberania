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

class PurchaseHistoryExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    protected $purchases;
    protected $stats;
    protected $startDate;
    protected $endDate;

    public function __construct($purchases, $stats, $startDate, $endDate)
    {
        $this->purchases = $purchases;
        $this->stats = $stats;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection(): Collection
    {
        $data = collect();
        
        $data->push(['HISTORIAL DE COMPRAS']);
        $data->push(['Periodo:', Carbon::parse($this->startDate)->format('d/m/Y'), 'al', Carbon::parse($this->endDate)->format('d/m/Y')]);
        $data->push(['Generado:', Carbon::now()->format('d/m/Y H:i:s')]);
        $data->push([]);
        
        $data->push(['RESUMEN']);
        $data->push(['Total de Órdenes:', $this->stats['total_orders']]);
        $data->push(['Total de Productos:', $this->stats['total_products']]);
        $data->push(['Total Gastado:', '$' . number_format($this->stats['total_spent'], 2)]);
        $data->push([]);
        $data->push([]);

        $data->push(['Número de Orden', 'Fecha', 'Producto', 'Código', 'Cantidad', 'Precio Unitario', 'Subtotal']);

        foreach ($this->purchases as $purchase) {
            $data->push([
                $purchase->order_number,
                Carbon::parse($purchase->order_date)->format('d/m/Y H:i'),
                $purchase->product_name,
                $purchase->product_code,
                $purchase->quantity,
                '$' . number_format($purchase->price, 2),
                '$' . number_format($purchase->subtotal, 2),
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
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            ],
            11 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Historial de Compras';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = count($this->purchases) + 11;
                $event->sheet->getStyle('A11:G' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('E12:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('F12:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}