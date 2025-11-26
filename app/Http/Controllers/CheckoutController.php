<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\NotificationService;

class CheckoutController extends Controller
{
    protected $notificationService;
    private string $routeName = 'checkout.';
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}store")->only(['store']);
        $this->middleware("permission:{$this->routeName}confirmation")->only(['confirmation']);
    }

    public function index()
    {
        $sessionCart = session()->get('cart', []);

        if (empty($sessionCart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío');
        }

        $productIds = array_keys($sessionCart);
        $products = Product::whereIn('id', $productIds)
            ->with(['photos'])
            ->get();

        $cartItems = $products->map(function ($product) use ($sessionCart) {
            $photoUrl = $product->photos->first()?->url ?? '/images/default-product.png';

            return [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'photo'    => $photoUrl,
                'quantity' => $sessionCart[$product->id]['quantity'],
            ];
        });

        $subtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);

        return Inertia::render('Checkout/Index', [
            'cartItems' => $cartItems->values()->all(),
            'subtotal' => number_format($subtotal, 2, '.', ''),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'required|email',
            'buyer_phone' => 'required|string|max:20',
            'delivery_type' => 'required|in:domicilio,recoleccion',
            'delivery_address' => 'nullable|string|max:500',
            'payment_method' => 'required|in:contra_entrega,transferencia',
        ]);

        $sessionCart = session()->get('cart', []);

        if (empty($sessionCart)) {
            return back()->with('error', 'Tu carrito está vacío');
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para completar el pedido.');
        }
        DB::beginTransaction();

        try {
            $productIds = array_keys($sessionCart);
            $products = Product::whereIn('id', $productIds)->get();

            foreach ($products as $product) {
                $requestedQuantity = $sessionCart[$product->id]['quantity'];
                if ($product->stock_quantity < $requestedQuantity) {
                    return back()->with('error', "Stock insuficiente para {$product->name}. Disponible: {$product->stock_quantity}");
                }
            }

            $totalAmount = $products->sum(function ($product) use ($sessionCart) {
                return $product->price * $sessionCart[$product->id]['quantity'];
            });

            $isCooperative = $user->isCooperative();

            $order = Order::create([
                'consumer_id' => $isCooperative ? null : $user->id,
                'cooperative_id' => $isCooperative ? $user->id : null,
                'total_amount' => $totalAmount,
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($products as $product) {
                $quantity = $sessionCart[$product->id]['quantity'];

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                \App\Models\InventoryExit::create([
                    'product_id' => $product->id,
                    'order_item_id' => $orderItem->id,
                    'quantity' => $quantity,
                    'reason' => "Venta - Pedido #{$order->id}",
                    'user_id' => $user->id,
                ]);
                $product->decrement('stock_quantity', $quantity);
                $product->refresh();
                $lowStockThreshold = 5;
                if ($product->stock_quantity <= $lowStockThreshold && $product->stock_quantity > 0) {
                    $producer = $product->user;
                    if ($producer) {
                        app(NotificationService::class)->sendLowStockAlert($product, $producer);
                    }
                }

                if ($product->stock_quantity <= 0) {
                    $product->update(['is_available' => false]);
                }
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => Payment::STATUS_PENDING,
            ]);

            Delivery::create([
                'order_id' => $order->id,
                'estimated_delivery_date' => now()->addDays(3),
                'status' => Delivery::STATUS_PENDING_ASSIGNMENT,
            ]);

            session()->forget('cart');
            DB::commit();

            $this->notificationService->sendOrderConfirmation($order);
            return redirect()->route('orders.confirmation', $order->id)
                ->with('success', '¡Pedido realizado con éxito!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear pedido: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    public function confirmation(Order $order)
    {
        $userIsConsumer = ($order->consumer_id === Auth::id());
        $userIsCooperative = ($order->cooperative_id === Auth::id());

        if (!$userIsConsumer && !$userIsCooperative) {
            abort(403, 'No tienes permiso para ver esta confirmación de pedido.');
        }
        $order->load(['orderItems.product.photos', 'payment']);
        return Inertia::render('Checkout/Confirmation', [
            'order' => $order,
        ]);
    }
}
