<?php

namespace App\Services;

use App\Contracts\Geo\GeocodingProviderInterface;

/**
 * Delegador backward-compatible sobre GeocodingProviderInterface.
 *
 * Mantiene las mismas firmas públicas que la versión original para que
 * cualquier caller existente siga funcionando sin modificación.
 * La lógica real vive en el provider concreto (NominatimProvider por defecto).
 */
class NominatimService
{
    public function __construct(
        private readonly GeocodingProviderInterface $provider,
    ) {}

    /**
     * Geocodificación inversa. Devuelve array con claves:
     * display_name, direccion, calle, numero, distrito, provincia,
     * departamento, codigo_postal, pais, lat, lng.
     *
     * @return array<string, string|float>|null
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        return $this->provider->reverseGeocode($lat, $lng)?->toArray();
    }

    /**
     * Búsqueda de texto libre. Devuelve array crudo del proveedor o vacío en error.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, string $countryCode = 'pe'): array
    {
        return $this->provider->search($query, $countryCode);
    }
}
