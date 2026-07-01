<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Services\NotificationService;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Pedido::query()
            ->with(['cliente', 'negocioAfiliado'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('codigo', 'like', "%{$search}%")
                    ->orWhereHas('cliente', fn ($query) => $query->where('nombres', 'like', "%{$search}%")->orWhere('telefono', 'like', "%{$search}%"));
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', [
            'adminModules' => AdminNavigation::for('pedidos'),
            'orders' => $orders,
            'estadoOptions' => Pedido::estadoOptions(),
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    public function edit(Pedido $order): View
    {
        $availableCouriers = Repartidor::query()
            ->where(function ($query) use ($order): void {
                $query->where('estado', Repartidor::ESTADO_DISPONIBLE)
                    ->orWhere('id', $order->repartidor_id);
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        return view('admin.orders.edit', [
            'adminModules' => AdminNavigation::for('pedidos'),
            'order' => $order->load(['cliente', 'negocioAfiliado', 'repartidor', 'detalles', 'estados.user']),
            'estadoOptions' => Pedido::estadoOptions(),
            'availableCouriers' => $availableCouriers,
            'courierEstadoOptions' => Repartidor::estadoOptions(),
        ]);
    }

    public function update(UpdateOrderStatusRequest $request, Pedido $order, NotificationService $notifications): RedirectResponse
    {
        $previous = $order->estado;
        $data = $request->validated();

        $order->update(['estado' => $data['estado']]);

        if ($order->repartidor && in_array($data['estado'], [
            Pedido::ESTADO_ENTREGADO,
            Pedido::ESTADO_CANCELADO,
        ], true)) {
            $order->repartidor->update(['estado' => Repartidor::ESTADO_DISPONIBLE]);
        }

        $order->estados()->create([
            'user_id' => $request->user()->id,
            'estado_anterior' => $previous,
            'estado_nuevo' => $data['estado'],
            'comentario' => $data['comentario'] ?? null,
        ]);
        $notifications->orderStatusChanged($order->refresh(), $previous);

        return redirect()
            ->route('admin.orders.edit', $order)
            ->with('status', 'Estado del pedido actualizado correctamente.');
    }
}
