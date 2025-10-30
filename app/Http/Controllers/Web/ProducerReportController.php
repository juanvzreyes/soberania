<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProducerReportController extends Controller
{
    public function generateReport()
    {
        $producers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Producer');
        })
            ->with([
                'products',
                'producer.location',
            ])->get();
        $chartData = $this->prepareChartData($producers);
        $pdf = Pdf::loadView('reports.producers', [
            'producers' => $producers,
            'chartData' => $chartData,
        ]);
        return $pdf->download('productores.pdf');
    }

    private function prepareChartData($producers)
    {
        $labels = [];
        $data = [];

        foreach ($producers as $producer) {
            $labels[] = $producer->name;
            $data[] = $producer->products->count();
        }
        if (empty($labels)) {
            return ['url' => null];
        }
        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Cantidad de Productos',
                    'data' => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ]],
            ],
            'options' => [
                'title' => ['display' => true, 'text' => 'Productos Ofrecidos por Productor'],
                'legend' => ['display' => false],
            ],
        ];

        $quickChartUrl = "https://quickchart.io/chart";
        $response = Http::post($quickChartUrl, ['chart' => $chartConfig]);

        $base64Image = null;
        if ($response->successful()) {
            $imageData = $response->body();
            $base64Image = 'data:image/png;base64,' . base64_encode($imageData);
        } else {
            Log::error('QuickChart API request failed: ' . $response->body());
        }

        return ['url' => $base64Image];
    }
}
