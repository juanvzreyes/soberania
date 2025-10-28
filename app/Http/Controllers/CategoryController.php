<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;

class CategoryController extends Controller
{

    protected string $routeName = "categories.";
    protected string $source    = "Admin/Categories/Pages/";
    protected Category $model;

    public function __construct()
    {
        $this->model = new Category();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}store")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
        
    }
    public function index(Request $request): Response
    {
        $query = $this->model->query();

        if ($search = $request->query('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $categories = $query->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        $categoriesResource = CategoryResource::collection($categories);
        $filters = [
            'search' => $request->query('search', ''),
            'rows'   => $request->query('rows', 10),
        ];

        return Inertia::render("{$this->source}Index", [
            'title'      => 'Gestión de Categorías',
            'categories' => $categoriesResource,
            'routeName'  => $this->routeName,
            'filters'    => $filters,
        ]);
    }
    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title'     => 'Crear Nueva Categoría',
            'routeName' => $this->routeName,
        ]);
    }
    public function store(StoreCategoryRequest $request)
    {
        $this->model->create($request->all());
        return redirect()->route("{$this->routeName}index")
            ->with('success', '¡Categoría creada con éxito!');
    }
    public function edit(Category $category): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title'     => 'Editar Categoría: ' . $category->name,
            'routeName' => $this->routeName,
            'category'  => new CategoryResource($category),
        ]);
    }
    public function update(Request $request, Category $category)
    {
        if (array_keys($request->all()) == ['is_active'] || array_keys($request->except('_method')) == ['is_active']) {
            $category->update(['is_active' => $request->is_active]);

            $message = $request->is_active ? 'Categoría activada correctamente.' : 'Categoría dada de baja correctamente.';
            return redirect()->back()->with('success', $message);
        }
        $updateRequest = UpdateCategoryRequest::createFrom($request);
        $validatedData = $updateRequest->validated();
        $category->update($validatedData);
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Categoría modificada con éxito');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route("{$this->routeName}index")
            ->with('warning', 'Categoría eliminada lógicamente (de baja).');
    }

    public function show(string $id)
    {
        abort(404);
    }
}
