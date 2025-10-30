<?php

namespace App\Services;

use App\Exports\DashboardExport;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;

class DashboardService
{
    public function getDataForUser(User $user, string $startDate, string $endDate): ?array
    {
        return match (true) {
            $user->hasRole('Admin') => $this->getAdminDashboard($startDate, $endDate),
            $user->hasRole('Producer') => $this->getProducerDashboard($user, $startDate, $endDate),
            $user->hasRole('Consumer') => $this->getConsumerDashboard($user, $startDate, $endDate),
            $user->hasRole('Cooperative') => $this->getCooperativeDashboard($user, $startDate, $endDate),
            default => null,
        };
    }

    public function exportToExcel(string $startDate, string $endDate): BinaryFileResponse
    {
        $stats = $this->getAdminStats($startDate, $endDate);
        $filters = ['startDate' => $startDate, 'endDate' => $endDate];
        $filename = 'reporte-dashboard-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new DashboardExport($stats, $filters), $filename);
    }

    public function exportToPdf(string $startDate, string $endDate): Response
    {
        $stats = $this->getAdminStats($startDate, $endDate);
        $filters = ['startDate' => $startDate, 'endDate' => $endDate];
        $filename = 'reporte-dashboard-' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('exports.dashboard', compact('stats', 'filters'));

        return $pdf->download($filename);
    }

    private function getAdminStats(string $startDate, string $endDate): array
    {
        $activeProducers = User::role('Producer')->whereNull('deleted_at')->count();
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalSales = (float) Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereNull('products.deleted_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return [
            'primaryStat' => $activeProducers,
            'totalOrders' => $totalOrders,
            'totalSales' => $totalSales,
            'topProducts' => $topProducts,
        ];
    }

    private function getAdminDashboard(string $startDate, string $endDate): array
    {
        $stats = $this->getAdminStats($startDate, $endDate);
        return [
            'view' => 'Dashboard/Pages/Index',
            'props' => [
                'stats' => $stats,
                'filters' => ['startDate' => $startDate, 'endDate' => $endDate]
            ]
        ];
    }

    private function getProducerDashboard(User $user, string $startDate, string $endDate): array
    {
        $producerUserId = $user->id;

        $queryBase = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('products.user_id', $producerUserId)
            ->whereBetween('orders.created_at', [$startDate, $endDate]);

        $totalListedProducts = DB::table('products')
            ->where('user_id', $producerUserId)
            ->whereNull('deleted_at')
            ->count();

        $stats = [
            'primaryStat' => $totalListedProducts,
            'totalOrders' => (clone $queryBase)->distinct('order_items.order_id')->count(),
            'totalSales' => (float) (clone $queryBase)->sum(DB::raw('order_items.quantity * order_items.price')),
            'topProducts' => (clone $queryBase)
                ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('products.name')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get(),
        ];

        return [
            'view' => 'Dashboard/Pages/Index',
            'props' => [
                'stats' => $stats,
                'filters' => ['startDate' => $startDate, 'endDate' => $endDate]
            ]
        ];
    }

    private function getConsumerDashboard(User $user, string $startDate, string $endDate): array
    {
        $statsData = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.consumer_id', $user->id)
            ->select(
                DB::raw('COUNT(DISTINCT orders.id) as totalOrders'),
                DB::raw('SUM(orders.total_amount) as totalSales'),
                DB::raw('SUM(order_items.quantity) as totalProductsPurchased')
            )
            ->first();

        $stats = [
            'totalOrders' => (int) ($statsData->totalOrders ?? 0),
            'totalSales' => (float) ($statsData->totalSales ?? 0.0),
            'totalProductsPurchased' => (int) ($statsData->totalProductsPurchased ?? 0),
        ];

        $recentOrders = Order::where('consumer_id', $user->id)
            ->select('id', 'created_at', 'status', 'total_amount')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            'view' => 'Dashboard/Consumer/Index',
            'props' => [
                'stats' => $stats,
                'recentOrders' => $recentOrders,
            ]
        ];
    }
    private function getCooperativeDashboard(User $user, string $startDate, string $endDate): array
    {
        $statsData = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.cooperative_id', $user->id)
            ->select(
                DB::raw('COUNT(DISTINCT orders.id) as totalOrders'),
                DB::raw('SUM(orders.total_amount) as totalSales'),
                DB::raw('SUM(order_items.quantity) as totalProductsPurchased')
            )
            ->first();

        $stats = [
            'totalOrders' => (int) ($statsData->totalOrders ?? 0),
            'totalSales' => (float) ($statsData->totalSales ?? 0.0),
            'totalProductsPurchased' => (int) ($statsData->totalProductsPurchased ?? 0),
        ];

        $recentOrders = Order::where('cooperative_id', $user->id)
            ->select('id', 'created_at', 'status', 'total_amount')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            'view' => 'Dashboard/Cooperative/Index',
            'props' => [
                'stats' => $stats,
                'recentOrders' => $recentOrders,
            ]
        ];
    }
}
