<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Nominatim (OpenStreetMap) geocoding service.
 * Used for server-side reverse geocoding when batch processing or
 * when client-side calls are not appropriate.
 */
class NominatimService
{
    private const BASE_URL = 'https://nominatim.openstreetmap.org';

    private const TIMEOUT = 5;

    private const USER_AGENT = 'TiempoDelivery/1.0';

    /**
     * Reverse geocode coordinates into a structured address.
     * Returns null on connection failure or invalid response.
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders([
                    'User-Agent'      => self::USER_AGENT,
                    'Accept-Language' => 'es',
                ])
                ->get(self::BASE_URL . '/reverse', [
                    'lat'    => $lat,
                    'lon'    => $lng,
                    'format' => 'json',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (empty($data['address'])) {
                return null;
            }

            return $this->parseAddress($data);

        } catch (ConnectionException $e) {
            Log::warning('NominatimService: connection failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Forward geocoding: search by query string.
     * Returns array of results or empty array.
     */
    public function search(string $query, string $countryCode = 'pe'): array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders([
                    'User-Agent'      => self::USER_AGENT,
                    'Accept-Language' => 'es',
                ])
                ->get(self::BASE_URL . '/search', [
                    'q'            => $query,
                    'format'       => 'json',
                    'limit'        => 5,
                    'countrycodes' => $countryCode,
                ]);

            return $response->successful() ? ($response->json() ?? []) : [];

        } catch (ConnectionException $e) {
            Log::warning('NominatimService: search failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Parse a Nominatim JSON response into a structured address array
     * following Peru's administrative hierarchy:
     * País → Departamento (state) → Provincia (county) → Distrito (city_district).
     */
    private function parseAddress(array $data): array
    {
        $a = $data['address'] ?? [];

        $road       = $a['road'] ?? $a['pedestrian'] ?? $a['footway'] ?? $a['path'] ?? '';
        $houseNum   = $a['house_number'] ?? '';
        $fullStreet = implode(', ', array_filter([$road, $houseNum]));

        return [
            'display_name'  => $data['display_name'] ?? '',
            'direccion'     => $fullStreet,
            'calle'         => $road,
            'numero'        => $houseNum,
            'distrito'      => $a['city_district'] ?? $a['suburb'] ?? $a['municipality'] ?? $a['town'] ?? $a['village'] ?? '',
            'provincia'     => $a['county'] ?? $a['city'] ?? '',
            'departamento'  => $a['state'] ?? '',
            'codigo_postal' => $a['postcode'] ?? '',
            'pais'          => $a['country'] ?? '',
            'lat'           => (float) ($data['lat'] ?? 0),
            'lng'           => (float) ($data['lon'] ?? 0),
        ];
    }
}
