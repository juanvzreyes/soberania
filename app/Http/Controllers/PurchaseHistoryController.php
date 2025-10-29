<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\Filterable;

class PurchaseHistoryController extends Controller
{
    use Filterable;

    private string $source;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'PurchaseHistory/Pages/';
        $this->routeName = 'purchase-history.';
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}show")->only(['show']);
        $this->middleware("permission:{$this->routeName}reorder")->only(['reorder']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $user = Auth::user();
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Order::with(['orderItems.product.photos', 'payment', 'delivery'])
            ->orderBy('created_at', 'desc');

        if ($user->isConsumer()) {
            $query->where('consumer_id', $user->id);
        } elseif ($user->isCooperative()) {
            $query->where('cooperative_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate($filters->rows)->withQueryString();

        // Calcular el número de pedido relativo para cada usuario
        $totalOrders = Order::where(function ($q) use ($user) {
            if ($user->isConsumer()) {
                $q->where('consumer_id', $user->id);
            } elseif ($user->isCooperative()) {
                $q->where('cooperative_id', $user->id);
            }
        })->count();

        // Agregar número de pedido relativo a cada orden
        foreach ($orders as $index => $order) {
            // Contar cuántos pedidos hay ANTES de este pedido para el usuario
            $ordersBefore = Order::where(function ($q) use ($user) {
                if ($user->isConsumer()) {
                    $q->where('consumer_id', $user->id);
                } elseif ($user->isCooperative()) {
                    $q->where('cooperative_id', $user->id);
                }
            })
                ->where('created_at', '<', $order->created_at)
                ->count();

            $order->order_number = $ordersBefore + 1;
        }

        $totalSpent = Order::where(function ($q) use ($user) {
            if ($user->isConsumer()) {
                $q->where('consumer_id', $user->id);
            } elseif ($user->isCooperative()) {
                $q->where('cooperative_id', $user->id);
            }
        })
            ->where('status', '!=', Order::STATUS_CANCELED)
            ->sum('total_amount');

        return Inertia::render("{$this->source}Index", [
            'orders' => $orders,
            'filters' => [
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'statusOptions' => Order::getStatusOptions(),
            'totalSpent' => number_format($totalSpent, 2),
            'totalOrders' => $totalOrders,
            'title' => 'Mi Historial de Compras',
            'routeName' => $this->routeName,
        ]);
    }

    public function show(Order $order): Response
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            if ($user->isConsumer() && $order->consumer_id !== $user->id) {
                abort(403, 'No tienes permiso para ver este pedido.');
            }
            if ($user->isCooperative() && $order->cooperative_id !== $user->id) {
                abort(403, 'No tienes permiso para ver este pedido.');
            }
        }

        $order->load([
            'consumer',
            'cooperative',
            'orderItems.product.photos',
            'payment',
            'delivery'
        ]);

        // Calcular el número de pedido relativo
        $ordersBefore = Order::where(function ($q) use ($user, $order) {
            if ($user->isConsumer()) {
                $q->where('consumer_id', $user->id);
            } elseif ($user->isCooperative()) {
                $q->where('cooperative_id', $user->id);
            } elseif ($user->isAdmin()) {
                // Para admin, usar el mismo usuario del pedido
                if ($order->consumer_id) {
                    $q->where('consumer_id', $order->consumer_id);
                } elseif ($order->cooperative_id) {
                    $q->where('cooperative_id', $order->cooperative_id);
                }
            }
        })
            ->where('created_at', '<', $order->created_at)
            ->count();

        $order->order_number = $ordersBefore + 1;

        return Inertia::render("{$this->source}Show", [
            'order' => $order,
            'title' => 'Detalle de Compra #' . $order->order_number,
            'routeName' => $this->routeName,
        ]);
    }

    public function reorder(Request $request, Order $order)
    {
        $user = Auth::user();

        if ($user->isConsumer() && $order->consumer_id !== $user->id) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }
        if ($user->isCooperative() && $order->cooperative_id !== $user->id) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $cart = session()->get('cart', []);
        $addedProducts = [];
        $unavailableProducts = [];

        $orderItems = $order->orderItems;
        if (!empty($validated['product_ids'])) {
            $orderItems = $orderItems->whereIn('product_id', $validated['product_ids']);
        }

        foreach ($orderItems as $item) {
            $product = Product::find($item->product_id);

            if (!$product || !$product->is_available || $product->stock_quantity <= 0) {
                $unavailableProducts[] = $item->product->name;
                continue;
            }

            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $item->quantity;
            } else {
                $cart[$product->id] = [
                    "id" => $product->id,
                    "quantity" => min($item->quantity, $product->stock_quantity),
                ];
            }

            $addedProducts[] = $product->name;
        }

        session()->put('cart', $cart);
        $cartCount = collect($cart)->sum('quantity');

        $message = 'Productos agregados al carrito.';
        if (!empty($unavailableProducts)) {
            $message .= ' Algunos productos no están disponibles: ' . implode(', ', $unavailableProducts);
        }

        return redirect()->route('cart.index')
            ->with('success', $message)
            ->with(['cartCount' => $cartCount]);
    }
}
