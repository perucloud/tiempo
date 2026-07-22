<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Assignment\AlreadyAssignedException;
use App\Exceptions\Assignment\DriverNotAvailableException;
use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Services\DriverAssignmentService;
use App\Services\NotificationService;
use App\Support\AdminNavigation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsignacionController extends Controller
{
    public function __construct(
        private readonly DriverAssignmentService $assignments,
    ) {}

    /**
     * Pantalla de asignación con mapa — muestra candidatos y permite asignar.
     */
    public function show(Pedido $order): View
    {
        $order->load(['cliente', 'negocioAfiliado', 'repartidor', 'asignacionActiva']);

        return view('admin.asignacion.show', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'pedidos'),
            'order'        => $order,
        ]);
    }

    /**
     * JSON — lista de repartidores candidatos con distancia al negocio (llamado por AJAX).
     */
    public function candidatos(Pedido $order): JsonResponse
    {
        $negocio = $order->negocioAfiliado;

        if (! $negocio) {
            return response()->json(['data' => [], 'message' => 'El pedido no tiene negocio asociado.']);
        }

        $candidates = $this->assignments->findCandidates($negocio);

        return response()->json([
            'data' => array_map(fn ($c) => $c->toArray(), $candidates),
            'negocio' => [
                'nombre'   => $negocio->nombre_comercial,
                'latitud'  => $negocio->latitud,
                'longitud' => $negocio->longitud,
            ],
            'cliente' => [
                'nombre'   => $order->cliente?->nombres,
                'latitud'  => $order->latitud_cliente,
                'longitud' => $order->longitud_cliente,
            ],
        ]);
    }

    /**
     * POST — asigna el repartidor al pedido con protección anti-doble-asignación.
     */
    public function asignar(Request $request, Pedido $order, NotificationService $notifications): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'repartidor_id' => ['required', 'integer', 'exists:repartidores,id'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $repartidor = Repartidor::findOrFail($data['repartidor_id']);

        try {
            $asignacion = $this->assignments->assign(
                pedido: $order,
                repartidor: $repartidor,
                assignedBy: $request->user(),
                type: 'manual',
                notes: $data['notes'] ?? null,
            );

            /* Registrar en historial de estados */
            $order->refresh();
            $order->estados()->create([
                'user_id'         => $request->user()->id,
                'estado_anterior' => Pedido::ESTADO_CONFIRMADO,
                'estado_nuevo'    => Pedido::ESTADO_ASIGNADO,
                'comentario'      => "Repartidor asignado: {$repartidor->nombreCompleto()}",
            ]);

            $notifications->courierAssigned($order, $repartidor);

        } catch (AlreadyAssignedException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 409);
            }

            return redirect()->route('admin.orders.asignar', $order)
                ->with('error', $e->getMessage());

        } catch (DriverNotAvailableException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->route('admin.orders.asignar', $order)
                ->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message'       => "Repartidor {$repartidor->nombreCompleto()} asignado.",
                'asignacion_id' => $asignacion->id,
                'redirect'      => route('admin.orders.edit', $order),
            ]);
        }

        return redirect()
            ->route('admin.orders.edit', $order)
            ->with('status', "Repartidor {$repartidor->nombreCompleto()} asignado correctamente.");
    }
}
