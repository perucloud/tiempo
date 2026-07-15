<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Pedido;
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

    /** Actualiza la posición GPS del repartidor (llamado cada 10s desde el dispositivo) */
    public function updateCourierLocation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'repartidor_id' => ['required', 'integer', 'exists:repartidores,id'],
            'latitud'       => ['required', 'numeric', 'between:-90,90'],
            'longitud'      => ['required', 'numeric', 'between:-180,180'],
        ]);

        $repartidor = Repartidor::findOrFail($data['repartidor_id']);

        if ($repartidor->estado === Repartidor::ESTADO_INACTIVO) {
            return ApiResponse::error('El repartidor no esta activo.', status: 403);
        }

        $this->geo->updateCourierLocation($repartidor, (float) $data['latitud'], (float) $data['longitud']);

        return ApiResponse::success(
            ['actualizado_at' => now()->toIso8601String()],
            'Ubicacion actualizada.',
        );
    }

    /** Retorna la posición actual de un repartidor — solo para admin/operador desde admin routes */
    public function courierLocation(Repartidor $repartidor): JsonResponse
    {
        if (! $repartidor->latitud_actual) {
            return ApiResponse::error('El repartidor no tiene ubicacion registrada.', status: 404);
        }

        return ApiResponse::success([
            'repartidor_id'          => $repartidor->id,
            'nombre'                 => $repartidor->nombreCompleto(),
            'estado'                 => $repartidor->estado,
            'latitud'                => $repartidor->latitud_actual,
            'longitud'               => $repartidor->longitud_actual,
            'actualizado_at'         => $repartidor->ubicacion_actualizada_at?->toIso8601String(),
            'gps_activo'             => $repartidor->tieneGpsActivo(),
        ]);
    }

    /** Retorna todos los repartidores activos con ubicación — para el mapa del operador */
    public function activeCouriersLocations(): JsonResponse
    {
        $couriers = $this->geo->activeCouriersWithLocation()->map(fn (Repartidor $r) => [
            'id'             => $r->id,
            'nombre'         => $r->nombreCompleto(),
            'estado'         => $r->estado,
            'latitud'        => $r->latitud_actual,
            'longitud'       => $r->longitud_actual,
            'actualizado_at' => $r->ubicacion_actualizada_at?->toIso8601String(),
            'gps_activo'     => $r->tieneGpsActivo(),
        ]);

        return ApiResponse::success($couriers->values()->all(), meta: ['total' => $couriers->count()]);
    }
}
