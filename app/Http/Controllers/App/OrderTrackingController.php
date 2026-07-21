<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /** Página de seguimiento de un pedido por su código */
    public function show(string $codigo): View|RedirectResponse
    {
        $pedido = Pedido::query()
            ->with([
                'cliente',
                'negocioAfiliado',
                'detalles.producto',
                'estados',
                'repartidor',
                'pagos',
            ])
            ->where('codigo', $codigo)
            ->first();

        if (! $pedido || ! $this->canAccess($pedido)) {
            return redirect()
                ->route('app.home')
                ->with('order_error', "No encontramos el pedido {$codigo}.");
        }

        return view('app.order-tracking', compact('pedido'));
    }

    /** JSON polling — estado actual del pedido (llamado cada 15s desde el cliente) */
    public function estado(string $codigo): JsonResponse
    {
        $pedido = Pedido::query()
            ->with('repartidor')
            ->where('codigo', $codigo)
            ->firstOrFail();

        abort_unless($this->canAccess($pedido), 404);

        $rep = $pedido->repartidor;

        return response()->json([
            'estado'      => $pedido->estado,
            'estado_pago' => $pedido->estado_pago,
            'label'       => Pedido::ESTADOS_CLIENTE[$pedido->estado] ?? $pedido->estado,
            'repartidor'  => $rep ? [
                'nombre'           => $rep->nombreCompleto(),
                'latitud'          => $rep->latitud_actual,
                'longitud'         => $rep->longitud_actual,
                'gps_activo'       => $rep->tieneGpsActivo(),
                'estado_operativo' => $rep->estado_operativo,
            ] : null,
        ]);
    }

    /** JSON — historial de pedidos de un cliente por teléfono (panel perfil) */
    public function buscarPorTelefono(Request $request): JsonResponse
    {
        $data = $request->validate(['telefono' => ['required', 'string', 'max:30']]);

        if (! hash_equals((string) session('app_customer_phone', ''), $data['telefono'])) {
            return response()->json(['pedidos' => [], 'message' => 'No hay pedidos disponibles en esta sesion.']);
        }

        $cliente = Cliente::query()->where('telefono', $data['telefono'])->first();

        if (! $cliente) {
            return response()->json(['pedidos' => [], 'message' => 'Sin pedidos registrados para ese número.']);
        }

        $pedidos = $cliente->pedidos()
            ->with('negocioAfiliado')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Pedido $p) => [
                'codigo'  => $p->codigo,
                'negocio' => $p->negocioAfiliado?->nombre_comercial ?? '—',
                'total'   => 'S/ ' . number_format($p->total, 2),
                'estado'  => Pedido::ESTADOS_CLIENTE[$p->estado] ?? $p->estado,
                'hace'    => $p->created_at->diffForHumans(),
                'url'     => route('app.orders.show', $p->codigo),
            ]);

        return response()->json(['pedidos' => $pedidos->all()]);
    }

    private function canAccess(Pedido $pedido): bool
    {
        return in_array($pedido->id, array_map('intval', session('app_order_ids', [])), true);
    }
}
