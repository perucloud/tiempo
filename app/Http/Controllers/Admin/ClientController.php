<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Cliente;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Cliente::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', [
            'adminModules' => AdminNavigation::for('clientes'),
            'clients' => $clients,
            'estadoOptions' => Cliente::estadoOptions(),
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    public function create(): View
    {
        return view('admin.clients.form', [
            'adminModules' => AdminNavigation::for('clientes'),
            'client' => new Cliente(['estado' => Cliente::ESTADO_ACTIVO]),
            'estadoOptions' => Cliente::estadoOptions(),
            'action' => route('admin.clients.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Cliente::query()->create($request->validated());

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $client): View
    {
        return view('admin.clients.form', [
            'adminModules' => AdminNavigation::for('clientes'),
            'client' => $client,
            'estadoOptions' => Cliente::estadoOptions(),
            'action' => route('admin.clients.update', $client),
            'method' => 'PUT',
        ]);
    }

    public function update(UpdateClientRequest $request, Cliente $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Cliente desactivado correctamente.');
    }
}
