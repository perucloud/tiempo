<?php

namespace App\Services\Geo;

use App\Contracts\Geo\GeocodingProviderInterface;
use App\DTOs\Geo\GeocodeResult;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implementación de GeocodingProviderInterface usando Nominatim / OpenStreetMap.
 * Lee sus parámetros exclusivamente de config/geo.php — sin constantes hardcodeadas.
 */
class NominatimProvider implements GeocodingProviderInterface
{
    private string $baseUrl;
    private int    $timeout;
    private string $userAgent;
    private string $language;
    private int    $resultLimit;

    public function __construct()
    {
        $this->baseUrl     = config('geo.geocoding.base_url');
        $this->timeout     = config('geo.geocoding.timeout');
        $this->userAgent   = config('geo.geocoding.user_agent');
        $this->language    = config('geo.defaults.language', 'es');
        $this->resultLimit = config('geo.defaults.result_limit', 5);
    }

    public function reverseGeocode(float $lat, float $lng): ?GeocodeResult
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent'      => $this->userAgent,
                    'Accept-Language' => $this->language,
                ])
                ->get($this->baseUrl . '/reverse', [
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

            return GeocodeResult::fromNominatimResponse($data);

        } catch (ConnectionException $e) {
            Log::warning('NominatimProvider: reverse geocode connection failed', [
                'lat'   => $lat,
                'lng'   => $lng,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function search(string $query, string $countryCode = 'pe'): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent'      => $this->userAgent,
                    'Accept-Language' => $this->language,
                ])
                ->get($this->baseUrl . '/search', [
                    'q'            => $query,
                    'format'       => 'json',
                    'limit'        => $this->resultLimit,
                    'countrycodes' => $countryCode,
                ]);

            return $response->successful() ? ($response->json() ?? []) : [];

        } catch (ConnectionException $e) {
            Log::warning('NominatimProvider: search connection failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
