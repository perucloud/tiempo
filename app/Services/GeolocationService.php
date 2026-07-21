<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Repartidor;
use App\Models\RepartidorUbicacion;

class GeolocationService
{
    /** Mínimo de segundos entre actualizaciones de ubicación de repartidor */
    private const MIN_UPDATE_INTERVAL_SECONDS = 8;

    public function saveClientLocation(Pedido $pedido, float $latitud, float $longitud): void
    {
        $pedido->update([
            'latitud_cliente'    => $latitud,
            'longitud_cliente'   => $longitud,
            'geolocalizacion_at' => now(),
        ]);
    }

    /**
     * Actualiza la posición del repartidor con throttle anti-flood y precisión GPS opcional.
     * Retorna false si se descartó por frecuencia excesiva.
     */
    public function updateCourierLocation(
        Repartidor $repartidor,
        float $latitud,
        float $longitud,
        ?float $precision = null,
    ): bool {
        /* Throttle: no guardar si la última actualización fue hace menos de N segundos */
        if ($repartidor->ubicacion_actualizada_at !== null) {
            $elapsed = now()->diffInSeconds($repartidor->ubicacion_actualizada_at, absolute: true);
            if ($elapsed < self::MIN_UPDATE_INTERVAL_SECONDS) {
                return false;
            }
        }

        $repartidor->update([
            'latitud_actual'           => $latitud,
            'longitud_actual'          => $longitud,
            'ubicacion_actualizada_at' => now(),
        ]);

        RepartidorUbicacion::query()->create([
            'repartidor_id' => $repartidor->id,
            'pedido_id'     => $repartidor->pedidos()
                ->whereIn('estado', [Pedido::ESTADO_ASIGNADO, Pedido::ESTADO_EN_CAMINO])
                ->latest()
                ->value('id'),
            'latitud'      => $latitud,
            'longitud'     => $longitud,
            'precision_gps'=> $precision,
            'created_at'   => now(),
        ]);

        return true;
    }

    public function activeCouriersWithLocation(): \Illuminate\Database\Eloquent\Collection
    {
        return Repartidor::query()
            ->whereNotNull('latitud_actual')
            ->whereNotNull('longitud_actual')
            ->whereIn('estado', [Repartidor::ESTADO_DISPONIBLE, Repartidor::ESTADO_OCUPADO])
            ->where('ubicacion_actualizada_at', '>=', now()->subMinutes(10))
            ->get(['id', 'nombres', 'apellidos', 'estado', 'estado_operativo', 'latitud_actual', 'longitud_actual', 'ubicacion_actualizada_at']);
    }
}
