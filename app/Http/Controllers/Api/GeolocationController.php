<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Pedido;
use App\Models\PedidoAsignacion;
use App\Models\Repartidor;
use App\Services\GeolocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeolocationController extends Controller
{
    public function __construct(private readonly GeolocationService $geo) {}

    /** Guarda la ubicación del cliente al confirmar pedido desde /app */
    public function saveClientLocation(Request $request, string $codigo): JsonResponse
    {
        $data = $request->validate([
            'latitud'  => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $pedido = Pedido::query()->where('codigo', $codigo)->first();

        if (! $pedido) {
            return ApiResponse::error('Pedido no encontrado.', status: 404);
        }

        $this->geo->saveClientLocation($pedido, (float) $data['latitud'], (float) $data['longitud']);

        return ApiResponse::success(
            ['codigo' => $pedido->codigo, 'geolocalizacion_at' => $pedido->fresh()->geolocalizacion_at],
            'Ubicacion del cliente guardada.',
        );
    }

    /**
     * Actualiza la posición GPS del repartidor (llamado cada 10s desde el dispositivo).
     * Acepta precisión GPS opcional y aplica throttle anti-flood.
     */
    public function updateCourierLocation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'repartidor_id' => ['required', 'integer', 'exists:repartidores,id'],
            'latitud'       => ['required', 'numeric', 'between:-90,90'],
            'longitud'      => ['required', 'numeric', 'between:-180,180'],
            'precision'     => ['nullable', 'numeric', 'min:0', 'max:500'],
        ]);

        $repartidor = Repartidor::findOrFail($data['repartidor_id']);

        if ($repartidor->estado === Repartidor::ESTADO_INACTIVO) {
            return ApiResponse::error('El repartidor no esta activo.', status: 403);
        }

        $updated = $this->geo->updateCourierLocation(
            $repartidor,
            (float) $data['latitud'],
            (float) $data['longitud'],
            isset($data['precision']) ? (float) $data['precision'] : null,
        );

        return ApiResponse::success(
            ['actualizado_at' => now()->toIso8601String(), 'guardado' => $updated],
            $updated ? 'Ubicacion actualizada.' : 'Actualización ignorada (demasiado frecuente).',
        );
    }

    /**
     * Actualiza el estado operativo del repartidor.
     * Solo permite transiciones válidas según la máquina de estados.
     */
    public function updateCourierEstado(Request $request, Repartidor $repartidor): JsonResponse
    {
        $data = $request->validate([
            'estado_operativo' => ['required', 'string', 'in:' . implode(',', Repartidor::ESTADOS_OPERATIVOS)],
        ]);

        $nuevoEstado = $data['estado_operativo'];

        /* Verificar transición válida */
        if (! $repartidor->puedeTransicionar($nuevoEstado)) {
            $actual = $repartidor->estado_operativo ?? Repartidor::OP_OFFLINE;

            return ApiResponse::error(
                "Transición no permitida: {$actual} → {$nuevoEstado}.",
                status: 422,
            );
        }

        $updates = ['estado_operativo' => $nuevoEstado];

        /* Sincronizar estado de disponibilidad */
        if ($nuevoEstado === Repartidor::OP_AVAILABLE || $nuevoEstado === Repartidor::OP_DELIVERED) {
            $updates['estado'] = Repartidor::ESTADO_DISPONIBLE;
        }

        /* Completar asignación activa si el repartidor acaba de entregar */
        if ($nuevoEstado === Repartidor::OP_DELIVERED) {
            $asignacion = PedidoAsignacion::query()
                ->where('repartidor_id', $repartidor->id)
                ->where('status', PedidoAsignacion::STATUS_ACTIVO)
                ->first();

            if ($asignacion) {
                $asignacion->update([
                    'status'       => PedidoAsignacion::STATUS_COMPLETADO,
                    'completed_at' => now(),
                ]);

                /* Marcar pedido como entregado */
                $asignacion->pedido?->update(['estado' => Pedido::ESTADO_ENTREGADO]);
            }
        }

        $repartidor->update($updates);

        return ApiResponse::success(
            [
                'estado_operativo' => $repartidor->estado_operativo,
                'label'            => Repartidor::estadoOperativoLabel($nuevoEstado),
            ],
            'Estado actualizado.',
        );
    }

    /** Retorna la posición actual de un repartidor — solo para admin/operador */
    public function courierLocation(Repartidor $repartidor): JsonResponse
    {
        if (! $repartidor->latitud_actual) {
            return ApiResponse::error('El repartidor no tiene ubicacion registrada.', status: 404);
        }

        return ApiResponse::success([
            'repartidor_id'    => $repartidor->id,
            'nombre'           => $repartidor->nombreCompleto(),
            'estado'           => $repartidor->estado,
            'estado_operativo' => $repartidor->estado_operativo,
            'latitud'          => $repartidor->latitud_actual,
            'longitud'         => $repartidor->longitud_actual,
            'actualizado_at'   => $repartidor->ubicacion_actualizada_at?->toIso8601String(),
            'gps_activo'       => $repartidor->tieneGpsActivo(),
        ]);
    }

    /** Retorna todos los repartidores activos con ubicación — para el mapa del operador */
    public function activeCouriersLocations(): JsonResponse
    {
        $couriers = $this->geo->activeCouriersWithLocation()->map(fn (Repartidor $r) => [
            'id'               => $r->id,
            'nombre'           => $r->nombreCompleto(),
            'estado'           => $r->estado,
            'estado_operativo' => $r->estado_operativo ?? Repartidor::OP_OFFLINE,
            'latitud'          => $r->latitud_actual,
            'longitud'         => $r->longitud_actual,
            'actualizado_at'   => $r->ubicacion_actualizada_at?->diffForHumans(),
            'gps_activo'       => $r->tieneGpsActivo(),
        ]);

        return ApiResponse::success($couriers->values()->all(), meta: ['total' => $couriers->count()]);
    }

    /**
     * Retorna el pedido activo del repartidor con rutas — para la app del courier.
     */
    public function activePedido(Repartidor $repartidor): JsonResponse
    {
        $asignacion = PedidoAsignacion::query()
            ->with(['pedido.negocioAfiliado', 'pedido.cliente'])
            ->where('repartidor_id', $repartidor->id)
            ->where('status', PedidoAsignacion::STATUS_ACTIVO)
            ->first();

        if (! $asignacion) {
            return ApiResponse::success(null, 'Sin pedido activo.');
        }

        $pedido  = $asignacion->pedido;
        $negocio = $pedido?->negocioAfiliado;
        $cliente = $pedido?->cliente;

        return ApiResponse::success([
            'asignacion_id'      => $asignacion->id,
            'pedido_codigo'      => $pedido?->codigo,
            'pedido_id'          => $pedido?->id,
            'estado_operativo'   => $repartidor->estado_operativo,
            'negocio' => [
                'nombre'   => $negocio?->nombre_comercial,
                'direccion'=> $negocio?->direccion,
                'telefono' => $negocio?->telefono,
                'latitud'  => $negocio?->latitud,
                'longitud' => $negocio?->longitud,
            ],
            'cliente' => [
                'nombre'           => $cliente?->nombres,
                'telefono'         => $cliente?->telefono,
                'direccion_entrega'=> $pedido?->direccion_entrega,
                'latitud'          => $pedido?->latitud_cliente,
                'longitud'         => $pedido?->longitud_cliente,
            ],
            'rutas' => [
                'a_negocio'   => $asignacion->route_to_business,
                'a_cliente'   => $asignacion->route_to_customer,
                'km_negocio'  => $asignacion->distance_to_business_km,
                'km_cliente'  => $asignacion->distance_to_customer_km,
                'min_negocio' => $asignacion->estimated_time_to_business_min,
                'min_cliente' => $asignacion->estimated_time_to_customer_min,
            ],
        ]);
    }
}
