<?php

namespace App\DTOs\Geo;

/**
 * DTO inmutable que encapsula el resultado del cálculo de una ruta vial.
 *
 * La geometría se almacena en orden GeoJSON [[lng, lat], ...] tal como la
 * devuelve OSRM. El cliente JS la convierte a [[lat, lng]] para Leaflet.
 * Los valores distanceMeters y durationSeconds son los crudos del proveedor;
 * las variantes _kilometers y _minutes son derivadas sin redondear.
 * El redondeo se realiza únicamente para visualización en vistas o APIs.
 */
final readonly class RouteResult
{
    public function __construct(
        public bool   $routeFound,
        public float  $distanceMeters,
        public float  $distanceKilometers,
        public float  $durationSeconds,
        public float  $durationMinutes,
        public array  $geometry,      // [[lng, lat], ...] — orden GeoJSON
        public array  $origin,        // ['lat' => float, 'lng' => float]
        public array  $destination,   // ['lat' => float, 'lng' => float]
        public string $provider,
        public ?array $rawData = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'route_found'         => $this->routeFound,
            'distance_meters'     => $this->distanceMeters,
            'distance_kilometers' => $this->distanceKilometers,
            'duration_seconds'    => $this->durationSeconds,
            'duration_minutes'    => $this->durationMinutes,
            'geometry'            => $this->geometry,
            'origin'              => $this->origin,
            'destination'         => $this->destination,
            'provider'            => $this->provider,
        ];
    }

    public static function notFound(array $origin, array $destination, string $provider): self
    {
        return new self(
            routeFound:         false,
            distanceMeters:     0.0,
            distanceKilometers: 0.0,
            durationSeconds:    0.0,
            durationMinutes:    0.0,
            geometry:           [],
            origin:             $origin,
            destination:        $destination,
            provider:           $provider,
        );
    }
}
