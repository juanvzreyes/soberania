<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\ProductResource;

class CartController extends Controller
{
    private string $routeName = 'cart.';

    public function __construct()
    {
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}store")->only(['store']);
        $this->middleware("permission:{$this->routeName}update")->only(['update']);
        $this->middleware("permission:{$this->routeName}destroy")->only(['destroy']);
        $this->middleware("permission:{$this->routeName}clear")->only(['clear']);
    }
    public function store(Request $request, Product $product)
    {
        if ($product->stock_quantity <= 0 || !$product->is_available) {
            return redirect()->back()
                ->with('error', "Lo sentimos, {$product->name} no tiene stock disponible.");
        }

        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);
        $currentQuantityInCart = $cart[$product->id]['quantity'] ?? 0;
        $totalQuantity = $currentQuantityInCart + $quantity;

        if ($totalQuantity > $product->stock_quantity) {
            return redirect()->back()
                ->with('error', "Stock insuficiente. Solo quedan {$product->stock_quantity} unidades de {$product->name}.");
        }

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                "id"       => $product->id,
                "quantity" => $quantity,
            ];
        }

        session()->put('cart', $cart);

        $cartCount = collect($cart)->sum('quantity');

        return redirect()->back()
            ->with('success', "¡{$product->name} añadido al carrito!")
            ->with(['cartCount' => $cartCount]);
    }
    public function index()
    {
        $sessionCart = session()->get('cart', []);
        $productIds = array_keys($sessionCart);

        $products = Product::whereIn('id', $productIds)
            ->with(['photos'])
            ->get();

        $cartItems = $products->map(function ($product) use ($sessionCart) {
            $photoUrl = $product->photos->first()?->url ?? '/img/No-photo.jpg';

            return [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'photo'    => $photoUrl,
                'quantity' => $sessionCart[$product->id]['quantity'],
                'stock_quantity' => $product->stock_quantity,
            ];
        });

        $subtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);

        return Inertia::render('Cart/Pages/Index', [
            'title'        => 'Carrito de Compras',
            'cartItems'    => $cartItems->values()->all(),
            'cartSubtotal' => number_format($subtotal, 2),
        ]);
    }
    public function destroy(Product $product)
    {
        $cart = session()->get('cart', []);
        $productName = $product->name;

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        $cartCount = collect($cart)->sum('quantity');

        return redirect()->back()
            ->with('success', "{$productName} eliminado del carrito.")
            ->with(['cartCount' => $cartCount]);
    }
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Carrito vaciado correctamente.');
    }
    public function update(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if ($quantity > $product->stock_quantity) {
            return back()->with('error', "Stock insuficiente. Solo quedan {$product->stock_quantity} unidades de {$product->name}.");
        }

        if ($quantity <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id]['quantity'] = $quantity;
        }

        session()->put('cart', $cart);
        $cartCount = collect($cart)->sum('quantity');

        return redirect()->back()
            ->with('success', 'Cantidad actualizada')
            ->with(['cartCount' => $cartCount]);
    }
}
