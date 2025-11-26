<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\PhotoService;
use App\Services\ProductService;
use App\Traits\Filterable;
use App\DTOs\PhotoStorageConfig;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\NotificationService;

class ProductController extends Controller
{
    use Filterable;
    private Model $model;
    private string $source;
    private string $routeName;
    protected ProductService $productService;
    protected PhotoService $photoService;
    protected $notificationService;

    public function __construct(ProductService $productService, PhotoService $photoService, NotificationService $notificationService)
    {
        $this->source = 'Product/Pages/';
        $this->model = new Product();
        $this->routeName = 'products.';
        $this->productService = $productService;
        $this->photoService = $photoService;

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}store")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);

        $this->notificationService = $notificationService;
        // $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());

        $query = Product::query()
            ->with('category')
            ->where('name', 'LIKE', "%{$filters->search}%");

        $products = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'products'  => ProductResource::collection($products),
            'title'     => 'Gestión de Productos',
            'routeName' => $this->routeName,
            'filters'   => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title'      => 'Registrar Nuevo Producto',
            'routeName'  => $this->routeName,
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $this->productService->store($request->validated());
            return redirect()->route("{$this->routeName}index")->with('success', 'Producto creado con éxito.');
        } catch (Exception $exception) {
            Log::error("Error al crear producto: " . $exception->getMessage(), [$exception]);
            return redirect()->route("{$this->routeName}index")->with('error', 'Ha ocurrido un error al intentar crear el producto.');
        }
    }

    public function edit(Product $product): Response
    {
        $product->load('category', 'photos');
        return Inertia::render("{$this->source}Edit", [
            'title'      => 'Editar Producto: ' . $product->name,
            'routeName'  => $this->routeName,
            'product'    => new ProductResource($product),
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $keys = array_keys($request->except('_method', '_token'));

            if ($keys === ['is_available']) {
                $product->update(['is_available' => $request->is_available]);
                $message = $request->is_available
                    ? 'Producto activado correctamente.'
                    : 'Producto dado de baja correctamente.';

                return redirect()->back()->with('success', $message);
            }
            $validatedData = $request->validated();
            $this->productService->update($product, $validatedData);
            return redirect()->route("{$this->routeName}index")->with('success', 'Producto modificado con éxito.');
        } catch (\Exception $exception) {
            Log::error("Error al actualizar producto ID {$product->id}: " . $exception->getMessage(), [$exception]);

            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al intentar actualizar el producto.');
        }
    }
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route("{$this->routeName}index")->with('warning', 'Producto dado de baja (no disponible en el catálogo).');
        } catch (Exception $exception) {
            Log::error("Error al eliminar producto: " . $exception->getMessage(), [$exception]);
            return redirect()->route("{$this->routeName}index")->with('error', 'Ha ocurrido un error al intentar dar de baja el registro.');
        }
    }

    public function show(Product $product)
    {
        abort(404);
    }
}
