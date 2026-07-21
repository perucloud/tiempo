<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\PedidoAsignacion;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Services\DriverAssignmentService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function show(Repartidor $repartidor): View
    {
        abort_if(
            $repartidor->estado === Repartidor::ESTADO_INACTIVO,
            403,
            'Este repartidor no está activo en el sistema.'
        );

        $asignacion = PedidoAsignacion::query()
            ->with(['pedido.negocioAfiliado', 'pedido.cliente'])
            ->where('repartidor_id', $repartidor->id)
            ->where('status', PedidoAsignacion::STATUS_ACTIVO)
            ->first();

        return view('courier.turno', compact('repartidor', 'asignacion'));
    }

    /**
     * Actualiza el estado operativo del repartidor desde la app courier.
     * El botón de iniciar turno cambia el estado a "available".
     */
    public function updateEstado(
        Request $request,
        Repartidor $repartidor,
        DriverAssignmentService $assignments,
        NotificationService $notifications,
    ): JsonResponse
    {
        $data = $request->validate([
            'estado_operativo' => ['required', 'string', 'in:' . implode(',', Repartidor::ESTADOS_OPERATIVOS)],
        ]);

        $nuevoEstado = $data['estado_operativo'];

        /* Caso especial: iniciar turno (offline → available) permitido siempre */
        $esInicioTurno = $nuevoEstado === Repartidor::OP_AVAILABLE
            && ($repartidor->estado_operativo === Repartidor::OP_OFFLINE || ! $repartidor->estado_operativo);

        $esFinTurno = $nuevoEstado === Repartidor::OP_OFFLINE;

        if (! $esInicioTurno && ! $esFinTurno && ! $repartidor->puedeTransicionar($nuevoEstado)) {
            return response()->json([
                'message' => 'Transición de estado no permitida.',
                'actual'  => $repartidor->estado_operativo,
            ], 422);
        }

        $updates = ['estado_operativo' => $nuevoEstado];

        if ($esInicioTurno) {
            $updates['estado'] = Repartidor::ESTADO_DISPONIBLE;
        }

        if ($esFinTurno) {
            $updates['estado'] = Repartidor::ESTADO_INACTIVO;
        }

        $repartidor->update($updates);

        $asignacion = PedidoAsignacion::query()
            ->with('pedido')
            ->where('repartidor_id', $repartidor->id)
            ->where('status', PedidoAsignacion::STATUS_ACTIVO)
            ->first();

        if ($asignacion?->pedido && $nuevoEstado === Repartidor::OP_GOING_TO_CUSTOMER) {
            $pedido = $asignacion->pedido;
            $estadoAnterior = $pedido->estado;
            $pedido->update(['estado' => Pedido::ESTADO_EN_CAMINO]);
            $pedido->estados()->create([
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => Pedido::ESTADO_EN_CAMINO,
                'comentario' => 'El repartidor inició el trayecto hacia el cliente.',
            ]);
            $notifications->orderStatusChanged($pedido, $estadoAnterior);
        }

        if ($asignacion?->pedido && $nuevoEstado === Repartidor::OP_DELIVERED) {
            $pedido = $asignacion->pedido;
            $estadoAnterior = $pedido->estado;
            $pedido->update(['estado' => Pedido::ESTADO_ENTREGADO, 'entregado_at' => now()]);
            $pedido->estados()->create([
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => Pedido::ESTADO_ENTREGADO,
                'comentario' => 'Pedido marcado como entregado por el repartidor.',
            ]);
            $assignments->completeAssignment($asignacion);
            $notifications->orderStatusChanged($pedido, $estadoAnterior);
            $repartidor->refresh();
        }

        /* Reasignar los próximos botones según el nuevo estado */
        $siguientes = Repartidor::TRANSICIONES_OPERATIVAS[$nuevoEstado] ?? [];

        return response()->json([
            'estado_operativo' => $nuevoEstado,
            'label'            => Repartidor::estadoOperativoLabel($nuevoEstado),
            'siguientes'       => $siguientes,
        ]);
    }
}
