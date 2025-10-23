<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Inventory\StoreInventoryExitRequest;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryExit;
use Illuminate\Support\Facades\DB;

class InventoryExitController extends Controller
{
    use Filterable;

    private string $source = 'Inventory/Exit/Pages/';
    private string $routeName = 'inventoryExit.';
    protected InventoryExit $model;

    public function __construct()
    {
        $this->model = new InventoryExit();
        // $this->middleware('role:producer');
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());

        $query = InventoryExit::query()->with('product:id,name');
        if ($filters->search) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters->search}%");
            })->orWhere('reason', 'LIKE', "%{$filters->search}%");
        }
        $inventoryEntries = $query->orderBy('created_at', 'desc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'inventoryEntries'  => $inventoryEntries,
            'title'             => 'Historial de Salidas de Inventario',
            'routeName'         => $this->routeName,
            'filters'           => $filters,
        ]);
    }

    public function create(): Response
    {
        $products = Product::where('is_available', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'stock_quantity']);

        return Inertia::render("{$this->source}Create", [
            'title'     => 'Registro de Salida de Inventario', 
            'routeName' => $this->routeName,
            'products'  => $products,
        ]);
    }
    public function store(StoreInventoryExitRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = Product::find($request->product_id);

            if (!$product) {
                throw new Exception("El producto seleccionado no existe.");
            }
            if ($product->stock_quantity < $request->quantity) {
                throw new Exception("Stock insuficiente. El stock actual es {$product->stock_quantity} y se intentan sacar {$request->quantity}.");
            }
            $this->model->create($request->validated());
            $product->decrement('stock_quantity', $request->quantity);

            DB::commit();
            return redirect()->route("{$this->routeName}index")->with('success', 'Salida de inventario registrada con éxito.');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Error al registrar salida de inventario: " . $exception->getMessage(), [$exception]);
            return redirect()->back()->with('error', 'Error de Transacción: ' . $exception->getMessage());
        }
    }
}
