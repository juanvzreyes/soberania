<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseHistoryExport;
use App\Exports\TopProductsExport;
use Exception;

class ConsumerReportsController extends Controller
{
    private string $source;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'Reports/Consumer/';
        $this->routeName = 'consumer.reports.';
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}purchaseHistory")->only(['purchaseHistory', 'exportPurchaseHistory']);
        $this->middleware("permission:{$this->routeName}topProducts")->only(['topProducts', 'exportTopProducts']);
    }

    public function index(): Response
    {
        $user = Auth::user();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Mis Reportes',
            'routeName' => $this->routeName,
            'userName' => $user->name,
        ]);
    }

    public function purchaseHistory(Request $request)
    {
        try {
            $userId = Auth::id();

            Log::info('=== PURCHASE HISTORY REQUEST ===', [
                'user_id' => $userId,
                'user_email' => Auth::user()->email,
            ]);

            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $endDateWithTime = $endDate . ' 23:59:59';

            Log::info('Filtros aplicados', [
                'start_date' => $startDate,
                'end_date' => $endDateWithTime,
            ]);

            $allOrders = DB::table('orders')
                ->where('consumer_id', $userId)
                ->select('id', 'status', 'created_at', 'total_amount')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            Log::info('Últimas 5 órdenes del usuario', [
                'orders' => $allOrders->toArray()
            ]);

            $purchases = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.consumer_id', $userId)
                ->where('orders.status', 'Entregado')
                ->whereBetween('orders.created_at', [$startDate, $endDateWithTime])
                ->select(
                    'orders.id as order_id',
                    DB::raw('CONCAT("ORD-", LPAD(orders.id, 6, "0")) as order_number'),
                    'orders.created_at as order_date',
                    'orders.total_amount as total',
                    'products.name as product_name',
                    'products.id as product_id',
                    DB::raw('CONCAT("PROD-", products.id) as product_code'),
                    'order_items.quantity',
                    'order_items.price',
                    DB::raw('(order_items.quantity * order_items.price) as subtotal')
                )
                ->orderBy('orders.created_at', 'desc')
                ->get();

            Log::info('Resultado de purchases', [
                'count' => $purchases->count(),
                'first_item' => $purchases->first()
            ]);

            $chartData = DB::table('orders')
                ->where('consumer_id', $userId)
                ->where('status', 'Entregado')
                ->whereBetween('created_at', [$startDate, $endDateWithTime])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            Log::info('Chart data', [
                'count' => $chartData->count()
            ]);

            $stats = [
                'total_orders' => $purchases->pluck('order_id')->unique()->count(),
                'total_products' => $purchases->sum('quantity'),
                'total_spent' => $purchases->pluck('order_id')->unique()->map(function ($orderId) use ($purchases) {
                    return $purchases->where('order_id', $orderId)->first()->total;
                })->sum(),
                'average_order' => 0,
            ];

            $stats['average_order'] = $stats['total_orders'] > 0
                ? $stats['total_spent'] / $stats['total_orders']
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'purchases' => $purchases,
                    'chartData' => $chartData,
                    'stats' => $stats,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]
                ]
            ]);
        } catch (Exception $e) {
            Log::error("Error al generar reporte de historial de compras", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function topProducts(Request $request)
    {
        try {
            $userId = Auth::id();
            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $endDateWithTime = $endDate . ' 23:59:59';
            $categoryId = $request->input('category_id');
            $limit = $request->input('limit', 10);

            $query = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.consumer_id', $userId)
                ->where('orders.status', 'Entregado')
                ->whereBetween('orders.created_at', [$startDate, $endDateWithTime]);

            if ($categoryId) {
                $query->where('products.category_id', $categoryId);
            }

            $topProducts = $query
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('CONCAT("PROD-", products.id) as code'),
                    'categories.name as category_name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_spent'),
                    DB::raw('COUNT(DISTINCT orders.id) as times_purchased')
                )
                ->groupBy('products.id', 'products.name', 'categories.name')
                ->orderBy('total_quantity', 'desc')
                ->limit($limit)
                ->get();

            $categories = DB::table('categories')
                ->whereIn('id', function ($query) use ($userId) {
                    $query->select('products.category_id')
                        ->from('products')
                        ->join('order_items', 'products.id', '=', 'order_items.product_id')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.consumer_id', $userId)
                        ->where('orders.status', 'Entregado');
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'topProducts' => $topProducts,
                    'categories' => $categories,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'category_id' => $categoryId,
                        'limit' => $limit,
                    ]
                ]
            ]);
        } catch (Exception $e) {
            Log::error("Error al generar reporte de productos más comprados", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPurchaseHistory(Request $request)
    {
        try {
            $userId = Auth::id();
            $format = $request->input('format', 'pdf');

            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $endDateWithTime = $endDate . ' 23:59:59';

            Log::info('Export Purchase History', [
                'user_id' => $userId,
                'format' => $format,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $purchases = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.consumer_id', $userId)
                ->where('orders.status', 'Entregado')
                ->whereBetween('orders.created_at', [$startDate, $endDateWithTime])
                ->select(
                    'orders.id as order_id',
                    DB::raw('CONCAT("ORD-", LPAD(orders.id, 6, "0")) as order_number'),
                    'orders.created_at as order_date',
                    'orders.total_amount as total',
                    'products.name as product_name',
                    DB::raw('CONCAT("PROD-", products.id) as product_code'),
                    'order_items.quantity',
                    'order_items.price',
                    DB::raw('(order_items.quantity * order_items.price) as subtotal')
                )
                ->orderBy('orders.created_at', 'desc')
                ->get();

            Log::info('Purchases retrieved for export', [
                'count' => $purchases->count()
            ]);

            // Datos para la gráfica
            $chartData = DB::table('orders')
                ->where('consumer_id', $userId)
                ->where('status', 'Entregado')
                ->whereBetween('created_at', [$startDate, $endDateWithTime])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $stats = [
                'total_orders' => $purchases->pluck('order_id')->unique()->count(),
                'total_products' => $purchases->sum('quantity'),
                'total_spent' => $purchases->pluck('order_id')->unique()->map(function ($orderId) use ($purchases) {
                    return $purchases->where('order_id', $orderId)->first()->total;
                })->sum(),
            ];

            $user = Auth::user();
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

            if ($format === 'pdf') {
                // Preparar datos para la gráfica
                $chartDataFormatted = [
                    'labels' => $chartData->map(function ($item) {
                        $date = Carbon::createFromFormat('Y-m', $item->month);
                        return $date->locale('es')->isoFormat('MMM YYYY');
                    })->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Total Compras ($)',
                            'data' => $chartData->pluck('total_amount')->map(fn($v) => (float)$v)->toArray(),
                            'borderColor' => 'rgb(59, 130, 246)',
                            'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                            'fill' => true,
                            'tension' => 0.4
                        ]
                    ]
                ];

                // Generar URL de la gráfica
                $chartUrl = $this->generateChartUrl($chartDataFormatted, 'line');

                $pdf = Pdf::loadView('reports.consumer.purchase-history-pdf', [
                    'purchases' => $purchases,
                    'stats' => $stats,
                    'user' => $user,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'generatedAt' => Carbon::now()->format('d/m/Y H:i:s'),
                    'chartUrl' => $chartUrl,
                    'hasChart' => $chartData->isNotEmpty(),
                ]);

                $filename = "historial_compras_{$timestamp}.pdf";

                $this->createNotification(
                    'Reporte Generado',
                    "Historial de Compras exportado a PDF exitosamente",
                    'report_generated',
                    ['filename' => $filename, 'type' => 'purchase_history']
                );

                return $pdf->download($filename);
            } else {
                $filename = "historial_compras_{$timestamp}.xlsx";

                $this->createNotification(
                    'Reporte Generado',
                    "Historial de Compras exportado a Excel exitosamente",
                    'report_generated',
                    ['filename' => $filename, 'type' => 'purchase_history']
                );

                return Excel::download(
                    new PurchaseHistoryExport($purchases, $stats, $startDate, $endDate),
                    $filename
                );
            }
        } catch (Exception $e) {
            Log::error("Error al exportar historial de compras", [
                'user_id' => Auth::id(),
                'format' => $format ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al exportar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportTopProducts(Request $request)
    {
        try {
            $userId = Auth::id();
            $format = $request->input('format', 'pdf');

            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $endDateWithTime = $endDate . ' 23:59:59';
            $categoryId = $request->input('category_id');
            $limit = $request->input('limit', 10);

            // Query base
            $query = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.consumer_id', $userId)
                ->where('orders.status', 'Entregado')
                ->whereBetween('orders.created_at', [$startDate, $endDateWithTime]);

            if ($categoryId) {
                $query->where('products.category_id', $categoryId);
            }

            $topProducts = $query
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('CONCAT("PROD-", products.id) as code'),
                    'categories.name as category_name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_spent'),
                    DB::raw('COUNT(DISTINCT orders.id) as times_purchased')
                )
                ->groupBy('products.id', 'products.name', 'categories.name')
                ->orderBy('total_quantity', 'desc')
                ->limit($limit)
                ->get();

            $user = Auth::user();
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

            if ($format === 'pdf') {
                // Preparar datos para la gráfica de barras
                $chartDataFormatted = [
                    'labels' => $topProducts->pluck('name')->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Cantidad',
                            'data' => $topProducts->pluck('total_quantity')->map(fn($v) => (int)$v)->toArray(),
                            'backgroundColor' => [
                                'rgb(59, 130, 246)',
                                'rgb(16, 185, 129)',
                                'rgb(245, 158, 11)',
                                'rgb(239, 68, 68)',
                                'rgb(139, 92, 246)',
                                'rgb(236, 72, 153)',
                                'rgb(20, 184, 166)',
                                'rgb(249, 115, 22)',
                                'rgb(6, 182, 212)',
                                'rgb(132, 204, 22)',
                            ],
                        ]
                    ]
                ];

                // Generar URL de la gráfica
                $chartUrl = $this->generateChartUrl($chartDataFormatted, 'bar');

                $pdf = Pdf::loadView('reports.consumer.top-products-pdf', [
                    'topProducts' => $topProducts,
                    'user' => $user,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'categoryId' => $categoryId,
                    'generatedAt' => Carbon::now()->format('d/m/Y H:i:s'),
                    'chartUrl' => $chartUrl,
                    'hasChart' => $topProducts->isNotEmpty(),
                ]);

                $filename = "productos_mas_comprados_{$timestamp}.pdf";

                $this->createNotification(
                    'Reporte Generado',
                    "Productos Más Comprados exportado a PDF exitosamente",
                    'report_generated',
                    ['filename' => $filename, 'type' => 'top_products']
                );

                return $pdf->download($filename);
            } else {
                $filename = "productos_mas_comprados_{$timestamp}.xlsx";

                $this->createNotification(
                    'Reporte Generado',
                    "Productos Más Comprados exportado a Excel exitosamente",
                    'report_generated',
                    ['filename' => $filename, 'type' => 'top_products']
                );

                return Excel::download(
                    new TopProductsExport($topProducts, $startDate, $endDate, $categoryId),
                    $filename
                );
            }
        } catch (Exception $e) {
            Log::error("Error al exportar productos más comprados", [
                'user_id' => Auth::id(),
                'format' => $format ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al exportar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    private function createNotification(string $title, string $message, string $type, array $data = []): void
    {
        if (class_exists('\App\Models\Notification')) {
            try {
                \App\Models\Notification::create([
                    'user_id' => Auth::id(),
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'data' => json_encode(array_merge($data, [
                        'timestamp' => Carbon::now()->toDateTimeString(),
                    ])),
                ]);
            } catch (Exception $e) {
                Log::warning("No se pudo crear notificación", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function generateChartUrl(array $chartData, string $type = 'line'): string
    {
        $config = [
            'type' => $type,
            'data' => [
                'labels' => $chartData['labels'] ?? [],
                'datasets' => $chartData['datasets'] ?? []
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top'
                    ],
                    'title' => [
                        'display' => false
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true
                    ]
                ]
            ]
        ];

        $chartConfig = json_encode($config);
        $encodedConfig = urlencode($chartConfig);

        return "https://quickchart.io/chart?c={$encodedConfig}&width=800&height=300&backgroundColor=white";
    }
}
