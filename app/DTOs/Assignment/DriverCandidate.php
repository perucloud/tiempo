<?php

namespace App\DTOs\Assignment;

use App\Models\Repartidor;

final class DriverCandidate
{
    public function __construct(
        public readonly Repartidor $driver,
        public readonly float $distanceToBusinessKm,
        public readonly float $estimatedMinutesToBusiness,
        public readonly array $routeGeometry,     // [[lng,lat],...] GeoJSON order
        public readonly bool $routeFound,
    ) {}

    public function toArray(): array
    {
        return [
            'repartidor_id'                  => $this->driver->id,
            'nombre'                         => $this->driver->nombreCompleto(),
            'vehiculo_tipo'                  => $this->driver->vehiculo_tipo,
            'vehiculo_placa'                 => $this->driver->vehiculo_placa,
            'telefono'                       => $this->driver->telefono,
            'estado_operativo'               => $this->driver->estado_operativo ?? Repartidor::OP_OFFLINE,
            'latitud'                        => $this->driver->latitud_actual,
            'longitud'                       => $this->driver->longitud_actual,
            'gps_activo'                     => $this->driver->tieneGpsActivo(),
            'actualizado_at'                 => $this->driver->ubicacion_actualizada_at?->diffForHumans(),
            'distance_to_business_km'        => round($this->distanceToBusinessKm, 2),
            'estimated_minutes_to_business'  => round($this->estimatedMinutesToBusiness),
            'route_geometry'                 => $this->routeGeometry,
            'route_found'                    => $this->routeFound,
        ];
    }
}
