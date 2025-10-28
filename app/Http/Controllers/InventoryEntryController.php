<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Inventory\StoreInventoryEntryRequest;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryEntry;
use Illuminate\Support\Facades\DB;

class InventoryEntryController extends Controller
{
    use Filterable;

    private string $source = 'Inventory/Entry/Pages/';
    private string $routeName = 'inventoryEntry.';
    protected InventoryEntry $model;

    public function __construct()
    {
        $this->model = new InventoryEntry();
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}store")->only(['store', 'create']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());

        $query = InventoryEntry::query()->with('product:id,name');
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
            'title'             => 'Historial de Entradas de Inventario',
            'routeName'         => $this->routeName,
            'filters'           => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title'     => 'Entrada de Inventario',
            'routeName' => $this->routeName,
            'products' => Product::all(['id', 'name']),
        ]);
    }
    public function store(StoreInventoryEntryRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->model->create($request->validated());
            $product = Product::find($request->product_id);
            if (!$product) {
                throw new Exception("El producto no existe o fue eliminado.");
            }
            if ($request->quantity <= 0) {
                throw new Exception("La cantidad debe ser positiva.");
            }
            $product->increment('stock_quantity', $request->quantity);
            DB::commit();
            return redirect()->route("{$this->routeName}index")->with('success', 'Cantidad de producto agregada con éxito.');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Error al registrar entrada de inventario: " . $exception->getMessage(), [$exception]);
            return redirect()->back()->with('error', 'Error de Transacción: ' . $exception->getMessage());
        }
    }
}
