<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBusinessRequest;
use App\Http\Requests\Admin\UpdateBusinessRequest;
use App\Models\NegocioAfiliado;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(Request $request): View
    {
        $businesses = NegocioAfiliado::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('nombre_comercial', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%")
                        ->orWhere('ruc', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('tipo_negocio'), fn ($query) => $query->where('tipo_negocio', $request->string('tipo_negocio')))
            ->orderBy('nombre_comercial')
            ->paginate(10)
            ->withQueryString();

        return view('admin.businesses.index', [
            'adminModules' => AdminNavigation::for('negocios'),
            'businesses' => $businesses,
            'estadoOptions' => NegocioAfiliado::estadoOptions(),
            'tipoOptions' => NegocioAfiliado::tipoOptions(),
            'filters' => $request->only(['search', 'estado', 'tipo_negocio']),
        ]);
    }

    public function create(): View
    {
        return view('admin.businesses.form', [
            'adminModules' => AdminNavigation::for('negocios'),
            'business' => new NegocioAfiliado([
                'tipo_negocio' => NegocioAfiliado::TIPO_RESTAURANTE,
                'estado' => NegocioAfiliado::ESTADO_ACTIVO,
                'abierto' => false,
            ]),
            'estadoOptions' => NegocioAfiliado::estadoOptions(),
            'tipoOptions' => NegocioAfiliado::tipoOptions(),
            'action' => route('admin.businesses.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $data = $this->payload($request->validated());
        $data['slug'] = $this->uniqueSlug($data['nombre_comercial']);

        NegocioAfiliado::query()->create($data);

        return redirect()
            ->route('admin.businesses.index')
            ->with('status', 'Negocio afiliado creado correctamente.');
    }

    public function edit(NegocioAfiliado $business): View
    {
        return view('admin.businesses.form', [
            'adminModules' => AdminNavigation::for('negocios'),
            'business' => $business,
            'estadoOptions' => NegocioAfiliado::estadoOptions(),
            'tipoOptions' => NegocioAfiliado::tipoOptions(),
            'action' => route('admin.businesses.update', $business),
            'method' => 'PUT',
        ]);
    }

    public function update(UpdateBusinessRequest $request, NegocioAfiliado $business): RedirectResponse
    {
        $data = $this->payload($request->validated());

        if ($business->nombre_comercial !== $data['nombre_comercial']) {
            $data['slug'] = $this->uniqueSlug($data['nombre_comercial'], $business->id);
        }

        $business->update($data);

        return redirect()
            ->route('admin.businesses.index')
            ->with('status', 'Negocio afiliado actualizado correctamente.');
    }

    public function destroy(NegocioAfiliado $business): RedirectResponse
    {
        $business->delete();

        return redirect()
            ->route('admin.businesses.index')
            ->with('status', 'Negocio afiliado desactivado correctamente.');
    }

    private function payload(array $data): array
    {
        $text = $data['horarios_texto'] ?? null;
        if (blank($text) && (!blank($data['hora_apertura'] ?? null) || !blank($data['hora_cierre'] ?? null))) {
            $open  = $data['hora_apertura'] ?? '—';
            $close = $data['hora_cierre']   ?? '—';
            $text  = "{$open} – {$close}";
        }
        $data['horarios'] = $this->parseSchedule($text);
        unset($data['horarios_texto']);

        return $data;
    }

    private function parseSchedule(?string $schedule): ?array
    {
        if (blank($schedule)) {
            return null;
        }

        return ['general' => trim($schedule)];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (NegocioAfiliado::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
