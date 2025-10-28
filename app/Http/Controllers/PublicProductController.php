<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class PublicProductController extends Controller
{
    use Filterable;

    private string $source = 'Catalog/Pages/';
    private string $routeName = 'catalog.';

    public function __construct() {
        $this->middleware("permission:menu.catalog");
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());

        $query = Product::query()
            ->with(['producer', 'category', 'photos'])
            ->where('is_available', true);
        if ($filters->search) {
            $query->where('name', 'LIKE', "%{$filters->search}%");
        }
        $this->applyFilters($request, $query);
        $this->applyOrdering($query, $filters);

        $products = $query->paginate($filters->rows)
            ->withQueryString();
        $categories = Category::where('is_active', true)->get(['id', 'name']);

        return Inertia::render("{$this->source}Index", [
            'products'      => ProductResource::collection($products),
            'categories'    => $categories,
            'title'         => 'Catálogo de Productos AgroConecta',
            'routeName'     => $this->routeName,
            'filters'       => $filters,
        ]);
    }
    protected function applyFilters(Request $request, $query): void
    {
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('producer_id')) {
            $query->where('user_id', $request->producer_id);
        }
    }
    protected function applyOrdering($query, $filters): void
    {
        switch ($filters->order) {
            case 'price':
                $query->orderBy('price', $filters->direction);
                break;

            case 'best_sellers':
                $query->withCount(['orderItems as total_sold' => function ($q) {
                    $q->select(DB::raw('COALESCE(SUM(quantity), 0)'));
                }])
                    ->orderBy('total_sold', $filters->direction); 
                break;

            case 'stock_availability':
                $query->orderBy('stock_quantity', $filters->direction); 
                break;

            default:
                $query->orderBy('name', $filters->direction);
                break;
        }
    }
}
