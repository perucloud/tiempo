<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Categoria;
use App\Models\NegocioAfiliado;
use App\Models\Producto;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Producto::query()
            ->with(['negocioAfiliado', 'categoria'])
            ->when($request->filled('search'), fn ($query) => $query->where('nombre', 'like', '%'.$request->string('search')->toString().'%'))
            ->when($request->filled('negocio_afiliado_id'), fn ($query) => $query->where('negocio_afiliado_id', $request->integer('negocio_afiliado_id')))
            ->when($request->filled('categoria_id'), fn ($query) => $query->where('categoria_id', $request->integer('categoria_id')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'productos'),
            'products' => $products,
            'businesses' => $this->businessOptions(),
            'categories' => $this->categoryOptions(),
            'estadoOptions' => Producto::estadoOptions(),
            'filters' => $request->only(['search', 'negocio_afiliado_id', 'categoria_id', 'estado']),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'productos'),
            'product' => new Producto([
                'estado' => Producto::ESTADO_ACTIVO,
                'disponible' => true,
            ]),
            'businesses' => $this->businessOptions(),
            'categories' => $this->categoryOptions(),
            'estadoOptions' => Producto::estadoOptions(),
            'action' => route('admin.products.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['nombre'], (int) $data['negocio_afiliado_id']);

        Producto::query()->create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto creado correctamente.');
    }

    public function edit(Producto $product): View
    {
        return view('admin.products.form', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'productos'),
            'product' => $product,
            'businesses' => $this->businessOptions(),
            'categories' => $this->categoryOptions(),
            'estadoOptions' => Producto::estadoOptions(),
            'action' => route('admin.products.update', $product),
            'method' => 'PUT',
        ]);
    }

    public function update(UpdateProductRequest $request, Producto $product): RedirectResponse
    {
        $data = $request->validated();

        if ($product->nombre !== $data['nombre'] || $product->negocio_afiliado_id !== (int) $data['negocio_afiliado_id']) {
            $data['slug'] = $this->uniqueSlug($data['nombre'], (int) $data['negocio_afiliado_id'], $product->id);
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto desactivado correctamente.');
    }

    private function businessOptions()
    {
        return NegocioAfiliado::query()
            ->orderBy('nombre_comercial')
            ->get(['id', 'nombre_comercial']);
    }

    private function categoryOptions()
    {
        return Categoria::query()
            ->where('estado', Categoria::ESTADO_ACTIVO)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }

    private function uniqueSlug(string $name, int $businessId, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Producto::query()
            ->where('negocio_afiliado_id', $businessId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
