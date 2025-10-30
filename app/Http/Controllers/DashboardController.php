<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private string $source;

    public function __construct()
    {
        $this->source = "Dashboard/";
        $this->middleware('auth');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        switch (true) {
            case $user->hasRole('Admin'):
                $activeProducers = User::role('Producer')
                    ->whereNull('deleted_at')
                    ->count();

                $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
                $totalSales = (float) Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
                $topProducts = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->groupBy('products.name')
                    ->orderByDesc('total_sold')
                    ->limit(10)
                    ->get();

                return Inertia::render("{$this->source}Pages/Index", [
                    'stats' => [
                        'activeProducers' => $activeProducers,
                        'totalOrders' => $totalOrders,
                        'totalSales' => $totalSales,
                        'topProducts' => $topProducts,
                    ],
                    'filters' => ['startDate' => $startDate, 'endDate' => $endDate]
                ]);

            case $user->hasRole('Producer'):
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

                $totalOrders = (clone $queryBase)->distinct('order_items.order_id')->count();
                $totalSales = (float) (clone $queryBase)->sum(DB::raw('order_items.quantity * order_items.price'));

                $topProducts = (clone $queryBase)
                    ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->groupBy('products.name')
                    ->orderByDesc('total_sold')
                    ->limit(10)
                    ->get();

                return Inertia::render("{$this->source}Pages/Index", [
                    'stats' => [
                        'activeProducers' => $totalListedProducts,
                        'totalOrders' => $totalOrders,
                        'totalSales' => $totalSales,
                        'topProducts' => $topProducts,
                    ],
                    'filters' => ['startDate' => $startDate, 'endDate' => $endDate]
                ]);

            case $user->hasRole('Consumer'):
                $consumerId = $user->id;
                $stats = DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.consumer_id', $consumerId)
                    ->select(
                        DB::raw('COUNT(DISTINCT orders.id) as totalOrders'),
                        DB::raw('SUM(orders.total_amount) as totalSales'),
                        DB::raw('SUM(order_items.quantity) as totalProductsPurchased')
                    )
                    ->first();
                $recentOrders = Order::where('consumer_id', $consumerId)
                    ->select('id', 'created_at', 'status', 'total_amount')
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();

                return Inertia::render("{$this->source}Consumer/Index", [
                    'stats' => [
                        'totalOrders' => (int) ($stats->totalOrders ?? 0),
                        'totalSales' => (float) ($stats->totalSales ?? 0.0),
                        'totalProductsPurchased' => (int) ($stats->totalProductsPurchased ?? 0),
                    ],
                    'recentOrders' => $recentOrders,
                ]);

            case $user->hasRole('Cooperative'):
                $cooperativeId = $user->id;
                $stats = DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.cooperative_id', $cooperativeId)
                    ->select(
                        DB::raw('COUNT(DISTINCT orders.id) as totalOrders'),
                        DB::raw('SUM(orders.total_amount) as totalSales'),
                        DB::raw('SUM(order_items.quantity) as totalProductsPurchased')
                    )
                    ->first();
                $recentOrders = Order::where('cooperative_id', $cooperativeId)
                    ->select('id', 'created_at', 'status', 'total_amount')
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();

                return Inertia::render("{$this->source}Cooperative/Index", [
                    'stats' => [
                        'totalOrders' => (int) ($stats->totalOrders ?? 0),
                        'totalSales' => (float) ($stats->totalSales ?? 0.0),
                        'totalProductsPurchased' => (int) ($stats->totalProductsPurchased ?? 0),
                    ],
                    'recentOrders' => $recentOrders,
                ]);

            default:
                Auth::logout();
                return redirect('/');
        }
    }
}
