<?php

namespace App\Http\Controllers\Report;

use App\Exports\Producer\CustomerReportExport;
use App\Exports\Producer\InventoryReportExport;
use App\Exports\Producer\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ProducerDashboardReportController extends Controller
{
    protected string $routeName;
    protected string $source;

    public function __construct()
    {
        $this->routeName = "producer.reports.";
        $this->source    = "Report/Producer/";

        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}sales.export")->only(['generateSalesReportPdf', 'generateSalesReportExcel']);
        $this->middleware("permission:{$this->routeName}inventory.export")->only(['generateInventoryReportPdf','generateInventoryReportExcel']);
        $this->middleware("permission:{$this->routeName}customers.export")->only(['generateCustomerReportPdf','generateCustomerReportExcel']);
    }

    public function index(Request $request)
    {
        $reportType = $request->input('report_type');
        $salesReportData = null;
        $inventoryReportData = null;
        $customerReportData = null;

        if ($reportType === 'sales') {
            $salesReportData = $this->getSalesData($request);
        } elseif ($reportType === 'inventory') {
            $inventoryReportData = $this->getInventoryData($request);
        } elseif ($reportType === 'customers') {
            $customerReportData = $this->getCustomerData($request);
        }

        return Inertia::render("{$this->source}Index", [
            'title'               => 'Reportes de Productor',
            'routeName'           => $this->routeName,
            'salesReportData'     => $salesReportData,
            'inventoryReportData' => $inventoryReportData,
            'customerReportData'  => $customerReportData,
            'filters'             => $request->all(),
        ]);
    }

    public function generateSalesReportPdf(Request $request)
    {
        $data = $this->getSalesData($request);
        $pdf = Pdf::loadView('reports.producer.sales', $data);
        return $pdf->download('reporte-ventas.pdf');
    }

    public function generateSalesReportExcel(Request $request)
    {
        return Excel::download(new SalesReportExport($request->start_date, $request->end_date), 'reporte-ventas.xlsx');
    }

    public function generateInventoryReportPdf(Request $request)
    {
        $data = $this->getInventoryData($request);
        $pdf = Pdf::loadView('reports.producer.inventory', $data);
        return $pdf->download('reporte-inventario.pdf');
    }

    public function generateInventoryReportExcel(Request $request)
    {
        return Excel::download(new InventoryReportExport($request), 'reporte-inventario.xlsx');
    }

    public function generateCustomerReportPdf(Request $request)
    {
        $data = $this->getCustomerData($request);
        $pdf = Pdf::loadView('reports.producer.customers', $data);
        return $pdf->download('reporte-clientes.pdf');
    }

    public function generateCustomerReportExcel(Request $request)
    {
        return Excel::download(new CustomerReportExport($request->start_date, $request->end_date), 'reporte-clientes.xlsx');
    }

    private function getSalesData(Request $request): array
    {
        $producerId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $producerProductIds = Product::where('user_id', $producerId)->pluck('id');

        $orders = Order::query()
            ->whereHas('orderItems', function ($query) use ($producerProductIds) {
                $query->whereIn('product_id', $producerProductIds);
            })
            ->with([
                'consumer',
                'cooperative',
                'orderItems.product' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->orderByDesc('created_at')
            ->get();

        $orders->each(function ($order) use ($producerId) {
            $order->orderItems = $order->orderItems->filter(function ($item) use ($producerId) {
                return $item->product && $item->product->user_id == $producerId;
            });
        });

        $totalSales = $orders->flatMap->orderItems->sum(fn($item) => $item->quantity * $item->price);
        $totalOrders = $orders->count();

        $salesByDay = $orders->groupBy(fn($order) => $order->created_at->format('Y-m-d'))
            ->map(fn($dayOrders) => $dayOrders->flatMap->orderItems->sum(fn($item) => $item->quantity * $item->price))
            ->sortKeys();

        $chartUrl = $this->generateChart(
            'bar',
            $salesByDay->keys()->all(),
            $salesByDay->values()->all(),
            'Ventas por Día'
        );

        return compact('orders', 'totalSales', 'totalOrders', 'startDate', 'endDate', 'chartUrl');
    }

    private function getInventoryData(Request $request): array
    {
        $producerId = Auth::id();
        $lowStockThreshold = 10;

        $lowStockProducts = Product::where('user_id', $producerId)
            ->where('stock_quantity', '<=', $lowStockThreshold)
            ->orderBy('stock_quantity')
            ->get();

        $mostSoldProducts = Product::where('user_id', $producerId)
            ->with('category')
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('sum(quantity)'));
            }])
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        $pieChartData = $mostSoldProducts->where('total_sold', '>', 0);

        $chartUrl = $this->generateChart(
            'pie',
            $pieChartData->pluck('name')->all(),
            $pieChartData->pluck('total_sold')->all(),
            'Proporción de Productos Vendidos (Top 10)'
        );

        return compact('lowStockProducts', 'mostSoldProducts', 'chartUrl');
    }

    private function getCustomerData(Request $request): array
    {
        $producerId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $itemsSold = \App\Models\OrderItem::query()
            ->whereHas('product', function ($query) use ($producerId) {
                $query->where('user_id', $producerId);
            })
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
            })
            ->with('order:id,consumer_id,cooperative_id')
            ->get();

        $spendingByCustomer = $itemsSold->groupBy(function ($item) {
            return $item->order->consumer_id ?? $item->order->cooperative_id;
        })->map(function ($customerItems) {
            return $customerItems->sum(fn($item) => $item->quantity * $item->price);
        })->filter();

        if ($spendingByCustomer->isEmpty()) {
            return [
                'topCustomers' => collect(),
                'startDate' => $startDate,
                'endDate' => $endDate,
                'chartUrl' => null,
            ];
        }

        $topCustomerIds = $spendingByCustomer->sortDesc()->take(15)->keys();

        $topCustomers = User::find($topCustomerIds)
            ->map(function ($customer) use ($spendingByCustomer) {
                $customer->total_spent = $spendingByCustomer->get($customer->id);
                return $customer;
            })
            ->sortByDesc('total_spent')->values();

        $chartUrl = $this->generateChart(
            'bar',
            $topCustomers->pluck('name')->all(),
            $topCustomers->pluck('total_spent')->all(),
            'Top Clientes por Consumo'
        );

        return compact('topCustomers', 'startDate', 'endDate', 'chartUrl');
    }

    private function generateChart(string $type, array $labels, array $data, string $label): ?string
    {
        if (empty($labels) || empty($data)) {
            return null;
        }

        $chartConfig = [
            'type' => $type,
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $label,
                    'data' => $data,
                    'backgroundColor' => ($type == 'pie')
                        ? ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#E7E9ED', '#80E1A1', '#FFD700', '#DDA0DD']
                        : 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ]],
            ],
            'options' => [
                'title' => ['display' => true, 'text' => $label],
                'legend' => ['display' => ($type == 'pie')],
            ],
        ];

        try {
            $response = Http::timeout(5)->post('https://quickchart.io/chart', ['chart' => $chartConfig]);
            if ($response->successful()) {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Exception $e) {
            Log::error('QuickChart API request failed: ' . $e->getMessage());
        }

        return null;
    }
}
