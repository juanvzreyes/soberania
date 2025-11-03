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

class CooperativeOrdersByCategoryExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    protected $ordersByCategory;
    protected $startDate;
    protected $endDate;
    protected $categoryId;

    public function __construct($ordersByCategory, $startDate, $endDate, $categoryId = null)
    {
        $this->ordersByCategory = $ordersByCategory;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryId = $categoryId;
    }

    public function collection(): Collection
    {
        $data = collect();
        $data->push(['PEDIDOS POR CATEGORÍA - COOPERATIVA']);
        $data->push(['Periodo:', Carbon::parse($this->startDate)->format('d/m/Y'), 'al', Carbon::parse($this->endDate)->format('d/m/Y')]);
        $data->push(['Generado:', Carbon::now()->format('d/m/Y H:i:s')]);
        $data->push([]);
        $data->push(['Categoría', 'Total de Pedidos', 'Cantidad Total', 'Costo Total']);
        $totalOrders = 0;
        $totalQuantity = 0;
        $totalCost = 0;

        foreach ($this->ordersByCategory as $category) {
            $data->push([
                $category->display_category,
                $category->total_orders,
                $category->total_quantity,
                '$' . number_format($category->total_cost, 2),
            ]);

            $totalOrders += $category->total_orders;
            $totalQuantity += $category->total_quantity;
            $totalCost += $category->total_cost;
        }

        // Totales
        $data->push([]);
        $data->push([
            'TOTALES',
            $totalOrders,
            $totalQuantity,
            '$' . number_format($totalCost, 2),
        ]);

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
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Pedidos por Categoría';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = count($this->ordersByCategory) + 5;
                $event->sheet->getStyle('A5:D' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('B6:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('D6:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $totalRow = $lastRow + 2;
                $event->sheet->getStyle('A' . $totalRow . ':D' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                ]);
            },
        ];
    }
}