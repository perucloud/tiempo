<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourierRequest;
use App\Http\Requests\Admin\UpdateCourierRequest;
use App\Models\Repartidor;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourierController extends Controller
{
    public function index(Request $request): View
    {
        $couriers = Repartidor::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%")
                        ->orWhere('vehiculo_placa', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->withCount('pedidos')
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('admin.couriers.index', [
            'adminModules' => AdminNavigation::for('repartidores'),
            'couriers' => $couriers,
            'estadoOptions' => Repartidor::estadoOptions(),
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    public function create(): View
    {
        return view('admin.couriers.form', [
            'adminModules' => AdminNavigation::for('repartidores'),
            'courier' => new Repartidor(['estado' => Repartidor::ESTADO_DISPONIBLE]),
            'estadoOptions' => Repartidor::estadoOptions(),
            'action' => route('admin.couriers.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreCourierRequest $request): RedirectResponse
    {
        Repartidor::query()->create($request->validated());

        return redirect()
            ->route('admin.couriers.index')
            ->with('status', 'Repartidor creado correctamente.');
    }

    public function edit(Repartidor $courier): View
    {
        return view('admin.couriers.form', [
            'adminModules' => AdminNavigation::for('repartidores'),
            'courier' => $courier,
            'estadoOptions' => Repartidor::estadoOptions(),
            'action' => route('admin.couriers.update', $courier),
            'method' => 'PUT',
        ]);
    }

    public function update(UpdateCourierRequest $request, Repartidor $courier): RedirectResponse
    {
        $courier->update($request->validated());

        return redirect()
            ->route('admin.couriers.index')
            ->with('status', 'Repartidor actualizado correctamente.');
    }

    public function destroy(Repartidor $courier): RedirectResponse
    {
        $courier->delete();

        return redirect()
            ->route('admin.couriers.index')
            ->with('status', 'Repartidor desactivado correctamente.');
    }
}
