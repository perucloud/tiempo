<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Categoria;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Categoria::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('nombre', 'like', '%'.$request->string('search')->toString().'%');
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('tipo'), fn ($query) => $query->where('tipo', $request->string('tipo')))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'categorias'),
            'categories' => $categories,
            'estadoOptions' => Categoria::estadoOptions(),
            'tipoOptions' => Categoria::tipoOptions(),
            'filters' => $request->only(['search', 'estado', 'tipo']),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'categorias'),
            'category' => new Categoria([
                'tipo' => Categoria::TIPO_PRODUCTO,
                'estado' => Categoria::ESTADO_ACTIVO,
                'orden' => 0,
            ]),
            'estadoOptions' => Categoria::estadoOptions(),
            'tipoOptions' => Categoria::tipoOptions(),
            'action' => route('admin.categories.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['nombre']);

        Categoria::query()->create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoria creada correctamente.');
    }

    public function edit(Categoria $category): View
    {
        return view('admin.categories.form', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'categorias'),
            'category' => $category,
            'estadoOptions' => Categoria::estadoOptions(),
            'tipoOptions' => Categoria::tipoOptions(),
            'action' => route('admin.categories.update', $category),
            'method' => 'PUT',
        ]);
    }

    public function update(UpdateCategoryRequest $request, Categoria $category): RedirectResponse
    {
        $data = $request->validated();

        if ($category->nombre !== $data['nombre']) {
            $data['slug'] = $this->uniqueSlug($data['nombre'], $category->id);
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoria actualizada correctamente.');
    }

    public function destroy(Categoria $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoria desactivada correctamente.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Categoria::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
