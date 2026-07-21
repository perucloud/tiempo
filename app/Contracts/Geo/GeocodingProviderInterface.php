<?php

namespace App\Contracts\Geo;

use App\DTOs\Geo\GeocodeResult;

interface GeocodingProviderInterface
{
    /**
     * Convierte coordenadas GPS en un GeocodeResult estructurado.
     * Devuelve null si la petición falla o la respuesta no contiene dirección.
     */
    public function reverseGeocode(float $lat, float $lng): ?GeocodeResult;

    /**
     * Búsqueda de texto libre. Devuelve array crudo del proveedor o vacío en error.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, string $countryCode = 'pe'): array;
}
