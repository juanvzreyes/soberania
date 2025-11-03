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
use App\Exports\CooperativeOrdersExport;
use App\Exports\CooperativeOrdersByCategoryExport;
use Exception;

class CooperativeReportsController extends Controller
{
    private string $source;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'Reports/Cooperative/';
        $this->routeName = 'cooperative.reports.';

        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}orders")->only(['orders', 'exportOrders']);
        $this->middleware("permission:{$this->routeName}ordersByCategory")->only(['ordersByCategory', 'exportOrdersByCategory']);
    }

    public function index(): Response
    {
        $user = Auth::user();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Reportes de Cooperativa',
            'routeName' => $this->routeName,
            'cooperativeName' => $user->name,
        ]);
    }

    public function orders(Request $request)
    {
        try {
            $cooperativeId = Auth::id();
            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $dateRange = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

            Log::info("Consultando pedidos de cooperativa", [
                'cooperative_id' => $cooperativeId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            $ordersBaseQuery = DB::table('orders')
                ->where('cooperative_id', $cooperativeId)
                ->whereBetween('created_at', $dateRange);
            $totalRevenue = (clone $ordersBaseQuery)->sum('total_amount');
            $totalOrders = (clone $ordersBaseQuery)->count();
            $pendingOrders = (clone $ordersBaseQuery)->whereIn('status', ['Pendiente', 'En preparación'])->count();
            $completedOrders = (clone $ordersBaseQuery)->where('status', 'Entregado')->count();
            $chartData = (clone $ordersBaseQuery)
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            $orderItems = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('users', 'orders.consumer_id', '=', 'users.id')
                ->where('orders.cooperative_id', $cooperativeId)
                ->whereBetween('orders.created_at', $dateRange)
                ->select(
                    'orders.id as order_id',
                    DB::raw('COALESCE(users.name, "Comprador Eliminado") as consumer_name'),
                    'orders.created_at as order_date',
                    'orders.status',
                    'orders.total_amount',
                    'users.name as consumer_name',
                    'products.name as product_name',
                    'products.id as product_id',
                    DB::raw('CONCAT("PROD-", products.id) as product_code'),
                    'categories.name as category_name',
                    'order_items.quantity',
                    'order_items.price',
                    DB::raw('(order_items.quantity * order_items.price) as subtotal')
                )
                ->orderBy('orders.created_at', 'desc')
                ->get();

            Log::info("Ítems de pedidos encontrados", ['total' => $orderItems->count()]);
            $stats = [
                'total_orders' => $totalOrders,
                'total_products' => $orderItems->sum('quantity'),
                'total_revenue' => (float) $totalRevenue,
                'average_order' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
            ];
            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orderItems,
                    'chartData' => $chartData,
                    'stats' => $stats,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]
                ]
            ]);
        } catch (Exception $e) {
            Log::error("Error al generar reporte de pedidos de cooperativa", [
                'cooperative_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ordersByCategory(Request $request)
    {
        try {
            $cooperativeId = Auth::id();
            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $categoryId = $request->input('category_id');

            Log::info("Consultando pedidos por categoría", [
                'cooperative_id' => $cooperativeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category_id' => $categoryId
            ]);
            $query = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.cooperative_id', $cooperativeId)
                ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            if ($categoryId) {
                $query->where('products.category_id', $categoryId);
            }
            $ordersByCategory = (clone $query)
                ->select(
                    'categories.id as category_id',
                    'categories.name as category_name',
                    DB::raw('COALESCE(categories.name, "Sin categoría") as display_category'),
                    DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_cost')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('total_cost', 'desc')
                ->get();
            Log::info("Categorías encontradas", ['total' => $ordersByCategory->count()]);
            $orderDetails = (clone $query)
                ->select(
                    'orders.id as order_id',
                    DB::raw('CONCAT("ORD-", LPAD(orders.id, 6, "0")) as order_number'),
                    'orders.created_at as order_date',
                    'orders.status',
                    'orders.total_amount',
                    'categories.name as category_name',
                    DB::raw('COALESCE(categories.name, "Sin categoría") as display_category'),
                    'products.name as product_name',
                    'order_items.quantity',
                    'order_items.price',
                    DB::raw('(order_items.quantity * order_items.price) as subtotal')
                )
                ->orderBy('orders.created_at', 'desc')
                ->get();
            $categories = DB::table('categories')
                ->whereIn('id', function ($query) use ($cooperativeId) {
                    $query->select('products.category_id')
                        ->from('products')
                        ->join('order_items', 'products.id', '=', 'order_items.product_id')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.cooperative_id', $cooperativeId)
                        ->whereNotNull('products.category_id');
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'ordersByCategory' => $ordersByCategory,
                    'orderDetails' => $orderDetails,
                    'categories' => $categories,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'category_id' => $categoryId,
                    ]
                ]
            ]);
        } catch (Exception $e) {
            Log::error("Error al generar reporte de pedidos por categoría", [
                'cooperative_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportOrders(Request $request)
    {
        try {
            $cooperativeId = Auth::id();
            $format = $request->input('format', 'pdf');

            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $orders = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('users', 'orders.consumer_id', '=', 'users.id')
                ->where('orders.cooperative_id', $cooperativeId)
                ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    'orders.id as order_id',
                    DB::raw('CONCAT("ORD-", LPAD(orders.id, 6, "0")) as order_number'),
                    'orders.created_at as order_date',
                    'orders.status',
                    'orders.total_amount',
                    'users.name as consumer_name',
                    'products.name as product_name',
                    DB::raw('COALESCE(users.name, "Comprador Eliminado") as consumer_name'),
                    'categories.name as category_name',
                    'order_items.quantity',
                    'order_items.price',
                    DB::raw('(order_items.quantity * order_items.price) as subtotal')
                )
                ->orderBy('orders.created_at', 'desc')
                ->get();
            $chartData = DB::table('orders')
                ->where('cooperative_id', $cooperativeId)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $uniqueOrderIds = $orders->pluck('order_id')->unique();
            $totalRevenue = $uniqueOrderIds->reduce(function ($carry, $orderId) use ($orders) {
                $orderTotal = $orders->where('order_id', $orderId)->first()->total_amount ?? 0;
                return $carry + $orderTotal;
            }, 0);

            $stats = [
                'total_orders' => $uniqueOrderIds->count(),
                'total_products' => $orders->sum('quantity'),
                'total_revenue' => $totalRevenue,
                'pending_orders' => $orders->whereIn('status', ['Pendiente', 'En preparación'])->pluck('order_id')->unique()->count(),
                'completed_orders' => $orders->where('status', 'Entregado')->pluck('order_id')->unique()->count(),
            ];

            $user = Auth::user();
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

            if ($format === 'pdf') {
                $chartDataFormatted = [
                    'labels' => $chartData->map(function ($item) {
                        $date = Carbon::createFromFormat('Y-m', $item->month);
                        return $date->locale('es')->isoFormat('MMM YYYY');
                    })->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Pedidos',
                            'data' => $chartData->pluck('total_orders')->map(fn($v) => (int)$v)->toArray(),
                            'borderColor' => 'rgb(16, 185, 129)',
                            'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                            'fill' => true,
                            'tension' => 0.4
                        ]
                    ]
                ];

                $chartUrl = $this->generateChartUrl($chartDataFormatted, 'line');

                $pdf = Pdf::loadView('reports.cooperative.orders-pdf', [
                    'orders' => $orders,
                    'stats' => $stats,
                    'user' => $user,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'generatedAt' => Carbon::now()->format('d/m/Y H:i:s'),
                    'chartUrl' => $chartUrl,
                    'hasChart' => $chartData->isNotEmpty(),
                ]);

                $filename = "pedidos_cooperativa_{$timestamp}.pdf";

                $this->createNotification(
                    'Reporte Generado',
                    "Reporte de Pedidos exportado a PDF exitosamente",
                    'report_generated',
                    ['filename' => $filename, 'type' => 'cooperative_orders']
                );

                return $pdf->download($filename);
            } else {
                $filename = "pedidos_cooperativa_{$timestamp}.xlsx";

                $this->createNotification(
                    'Reporte Generado',
                    "Reporte de Pedidos exportado a Excel exitosamente",
                    'report_generated',
                    ['filename' => $filename, 'type' => 'cooperative_orders']
                );

                return Excel::download(
                    new CooperativeOrdersExport($orders, $stats, $startDate, $endDate),
                    $filename
                );
            }
        } catch (Exception $e) {
            Log::error("Error al exportar pedidos de cooperativa", [
                'cooperative_id' => Auth::id(),
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

    public function exportOrdersByCategory(Request $request)
    {
        try {
            $cooperativeId = Auth::id();
            $format = $request->input('format', 'pdf');

            $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $categoryId = $request->input('category_id');

            $query = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.cooperative_id', $cooperativeId)
                ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            if ($categoryId) {
                $query->where('products.category_id', $categoryId);
            }

            // ✅ SOLUCIÓN: Usar IFNULL para agrupar correctamente
            $ordersByCategory = $query
                ->select(
                    DB::raw('IFNULL(categories.id, 0) as category_id'),
                    DB::raw('IFNULL(categories.name, "Sin categoría") as category_name'),
                    DB::raw('IFNULL(categories.name, "Sin categoría") as display_category'),
                    DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_cost')
                )
                ->groupBy(DB::raw('IFNULL(categories.id, 0)'), DB::raw('IFNULL(categories.name, "Sin categoría")'))
                ->orderBy('total_cost', 'desc')
                ->get();

            // Validar que hay datos
            if ($ordersByCategory->isEmpty()) {
                Log::warning("No hay datos para exportar", [
                    'cooperative_id' => $cooperativeId,
                    'date_range' => [$startDate, $endDate],
                    'category_id' => $categoryId
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No hay datos para exportar en el periodo seleccionado'
                ], 404);
            }

            $user = Auth::user();
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

            if ($format === 'pdf') {
                try {
                    $chartDataFormatted = [
                        'labels' => $ordersByCategory->pluck('display_category')->toArray(),
                        'datasets' => [
                            [
                                'label' => 'Costo Total ($)',
                                'data' => $ordersByCategory->pluck('total_cost')->map(fn($v) => (float)$v)->toArray(),
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

                    $chartUrl = $this->generateChartUrl($chartDataFormatted, 'pie');

                    $pdf = Pdf::loadView('reports.cooperative.orders-by-category-pdf', [
                        'ordersByCategory' => $ordersByCategory,
                        'user' => $user,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'categoryId' => $categoryId,
                        'generatedAt' => Carbon::now()->format('d/m/Y H:i:s'),
                        'chartUrl' => $chartUrl,
                        'hasChart' => true,
                    ]);

                    $filename = "pedidos_por_categoria_{$timestamp}.pdf";

                    $this->createNotification(
                        'Reporte Generado',
                        "Pedidos por Categoría exportado a PDF exitosamente",
                        'report_generated',
                        ['filename' => $filename, 'type' => 'orders_by_category']
                    );

                    return $pdf->download($filename);
                } catch (\Exception $e) {
                    Log::error("Error al generar PDF", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                $filename = "pedidos_por_categoria_{$timestamp}.xlsx";

                $this->createNotification(
                    'Reporte Generado',
                    "Pedidos por Categoría exportado a Excel exitosamente",
                    'report_generated',
                    ['filename' => $filename, 'type' => 'orders_by_category']
                );

                return Excel::download(
                    new CooperativeOrdersByCategoryExport($ordersByCategory, $startDate, $endDate, $categoryId),
                    $filename
                );
            }
        } catch (Exception $e) {
            Log::error("Error al exportar pedidos por categoría", [
                'cooperative_id' => Auth::id(),
                'format' => $format ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al exportar el reporte: ' . $e->getMessage()
            ], 500);
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
                        'display' => $type === 'pie' || $type === 'doughnut',
                        'position' => 'right'
                    ],
                    'title' => [
                        'display' => false
                    ]
                ],
            ]
        ];

        if ($type !== 'pie' && $type !== 'doughnut') {
            $config['options']['scales'] = [
                'y' => [
                    'beginAtZero' => true
                ]
            ];
        }

        $chartConfig = json_encode($config);
        $encodedConfig = urlencode($chartConfig);

        return "https://quickchart.io/chart?c={$encodedConfig}&width=800&height=400&backgroundColor=white";
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
}
