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

class TopProductsExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    protected $topProducts;
    protected $startDate;
    protected $endDate;
    protected $categoryId;

    public function __construct($topProducts, $startDate, $endDate, $categoryId = null)
    {
        $this->topProducts = $topProducts;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryId = $categoryId;
    }

    public function collection(): Collection
    {
        $data = collect();

        // Agregar información del reporte
        $data->push(['PRODUCTOS MÁS COMPRADOS']);
        $data->push(['Periodo:', Carbon::parse($this->startDate)->format('d/m/Y'), 'al', Carbon::parse($this->endDate)->format('d/m/Y')]);
        $data->push(['Generado:', Carbon::now()->format('d/m/Y H:i:s')]);
        $data->push([]);

        // Encabezados de la tabla
        $data->push(['Ranking', 'Producto', 'Código', 'Categoría', 'Cantidad Total', 'Veces Comprado', 'Total Gastado']);

        // Datos de productos
        $ranking = 1;
        foreach ($this->topProducts as $product) {
            $data->push([
                $ranking++,
                $product->name,
                $product->code,
                $product->category_name ?? 'Sin categoría',
                $product->total_quantity,
                $product->times_purchased,
                '$' . number_format($product->total_spent, 2),
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
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Productos Más Comprados';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Aplicar bordes a toda la tabla de datos
                $lastRow = count($this->topProducts) + 5;
                $event->sheet->getStyle('A5:G' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Centrar columnas
                $event->sheet->getStyle('A6:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('E6:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('G6:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Resaltar los 3 primeros
                if (count($this->topProducts) >= 1) {
                    $event->sheet->getStyle('A6:G6')->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDE047']], // Oro
                        'font' => ['bold' => true],
                    ]);
                }
                if (count($this->topProducts) >= 2) {
                    $event->sheet->getStyle('A7:G7')->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1D5DB']], // Plata
                        'font' => ['bold' => true],
                    ]);
                }
                if (count($this->topProducts) >= 3) {
                    $event->sheet->getStyle('A8:G8')->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FB923C']], // Bronce
                        'font' => ['bold' => true],
                    ]);
                }
            },
        ];
    }
}