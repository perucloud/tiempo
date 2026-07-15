<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Repartidor;
use App\Models\RepartidorUbicacion;

class GeolocationService
{
    public function saveClientLocation(Pedido $pedido, float $latitud, float $longitud): void
    {
        $pedido->update([
            'latitud_cliente'    => $latitud,
            'longitud_cliente'   => $longitud,
            'geolocalizacion_at' => now(),
        ]);
    }

    public function updateCourierLocation(Repartidor $repartidor, float $latitud, float $longitud): void
    {
        $repartidor->update([
            'latitud_actual'          => $latitud,
            'longitud_actual'         => $longitud,
            'ubicacion_actualizada_at' => now(),
        ]);

        RepartidorUbicacion::query()->create([
            'repartidor_id' => $repartidor->id,
            'pedido_id'     => $repartidor->pedidos()
                ->whereIn('estado', [Pedido::ESTADO_ASIGNADO, Pedido::ESTADO_EN_CAMINO])
                ->latest()
                ->value('id'),
            'latitud'    => $latitud,
            'longitud'   => $longitud,
            'created_at' => now(),
        ]);
    }

    public function activeCouriersWithLocation(): \Illuminate\Database\Eloquent\Collection
    {
        return Repartidor::query()
            ->whereNotNull('latitud_actual')
            ->whereNotNull('longitud_actual')
            ->whereIn('estado', [Repartidor::ESTADO_DISPONIBLE, Repartidor::ESTADO_OCUPADO])
            ->where('ubicacion_actualizada_at', '>=', now()->subMinutes(10))
            ->get(['id', 'nombres', 'apellidos', 'estado', 'latitud_actual', 'longitud_actual', 'ubicacion_actualizada_at']);
    }
}
