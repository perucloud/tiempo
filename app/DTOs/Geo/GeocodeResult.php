<?php

namespace App\DTOs\Geo;

/**
 * DTO inmutable que encapsula el resultado de una geocodificación inversa.
 * Modela la jerarquía administrativa de Perú:
 * País → Departamento (state) → Provincia (county) → Distrito (city_district).
 */
final readonly class GeocodeResult
{
    public function __construct(
        public string $displayName,
        public string $direccion,
        public string $calle,
        public string $numero,
        public string $distrito,
        public string $provincia,
        public string $departamento,
        public string $codigoPostal,
        public string $pais,
        public float  $lat,
        public float  $lng,
    ) {}

    /**
     * Serializa al mismo formato que devolvía NominatimService::reverseGeocode()
     * antes de la refactorización — garantiza backward compatibility.
     *
     * @return array<string, string|float>
     */
    public function toArray(): array
    {
        return [
            'display_name'  => $this->displayName,
            'direccion'     => $this->direccion,
            'calle'         => $this->calle,
            'numero'        => $this->numero,
            'distrito'      => $this->distrito,
            'provincia'     => $this->provincia,
            'departamento'  => $this->departamento,
            'codigo_postal' => $this->codigoPostal,
            'pais'          => $this->pais,
            'lat'           => $this->lat,
            'lng'           => $this->lng,
        ];
    }

    /**
     * Factory: construye desde la respuesta JSON cruda de Nominatim /reverse.
     * Sigue la misma jerarquía que el parseAddress() original de NominatimService.
     *
     * @param array<string, mixed> $data
     */
    public static function fromNominatimResponse(array $data): self
    {
        $a = $data['address'] ?? [];

        $road       = $a['road'] ?? $a['pedestrian'] ?? $a['footway'] ?? $a['path'] ?? '';
        $houseNum   = $a['house_number'] ?? '';
        $fullStreet = implode(', ', array_filter([$road, $houseNum]));

        return new self(
            displayName:  $data['display_name'] ?? '',
            direccion:    $fullStreet,
            calle:        $road,
            numero:       $houseNum,
            distrito:     $a['city_district'] ?? $a['suburb'] ?? $a['municipality'] ?? $a['town'] ?? $a['village'] ?? '',
            provincia:    $a['county'] ?? $a['city'] ?? '',
            departamento: $a['state'] ?? '',
            codigoPostal: $a['postcode'] ?? '',
            pais:         $a['country'] ?? '',
            lat:          (float) ($data['lat'] ?? 0),
            lng:          (float) ($data['lon'] ?? 0),
        );
    }
}
