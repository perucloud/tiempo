<?php

namespace App\Services\Geo;

use App\Contracts\Geo\RoutingProviderInterface;
use App\DTOs\Geo\RouteResult;
use App\Exceptions\Geo\RoutingException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implementación de RoutingProviderInterface usando OSRM.
 * Lee todos los parámetros desde config/geo.php — sin constantes hardcodeadas.
 *
 * OSRM utiliza orden lng,lat en la URL (inverso al estándar lat,lng).
 * La geometría se almacena en orden GeoJSON [[lng, lat], ...];
 * la conversión a [[lat, lng]] para Leaflet ocurre en el cliente JS.
 *
 * URL pattern: {base_url}/route/v1/{profile}/{lng1},{lat1};{lng2},{lat2}
 */
class OsrmRoutingProvider implements RoutingProviderInterface
{
    private string $baseUrl;
    private int    $timeout;
    private string $profile;
    private string $userAgent;

    public function __construct()
    {
        $this->baseUrl   = rtrim((string) config('geo.routing.base_url'), '/');
        $this->timeout   = (int) config('geo.routing.timeout', 8);
        $this->profile   = (string) config('geo.routing.profile', 'driving');
        $this->userAgent = (string) config('geo.geocoding.user_agent', 'TiempoDelivery/1.0');
    }

    public function route(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng,
    ): RouteResult {
        $this->assertValidCoordinates($originLat, $originLng);
        $this->assertValidCoordinates($destLat, $destLng);

        $origin      = ['lat' => $originLat, 'lng' => $originLng];
        $destination = ['lat' => $destLat,   'lng' => $destLng];

        /* OSRM espera lng,lat (no lat,lng) */
        $coordinates = sprintf(
            '%s,%s;%s,%s',
            $originLng, $originLat,
            $destLng,   $destLat,
        );

        $url = "{$this->baseUrl}/route/v1/{$this->profile}/{$coordinates}";

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['User-Agent' => $this->userAgent])
                ->get($url, [
                    'overview'   => 'full',
                    'geometries' => 'geojson',
                    'steps'      => 'false',
                ]);

            if (! $response->successful()) {
                throw new RoutingException(
                    "OSRM respondió con HTTP {$response->status()} para la ruta solicitada.",
                );
            }

            return $this->parseResponse($response->json() ?? [], $origin, $destination);

        } catch (ConnectionException $e) {
            Log::warning('OsrmRoutingProvider: connection failed', [
                'origin'      => $origin,
                'destination' => $destination,
                'error'       => $e->getMessage(),
            ]);

            throw new RoutingException(
                "Sin conexión con el servidor de rutas OSRM: {$e->getMessage()}",
                0,
                $e,
            );
        }
    }

    private function parseResponse(array $data, array $origin, array $destination): RouteResult
    {
        $code = $data['code'] ?? '';

        /* Ruta no encontrada — OSRM lo indica con code != "Ok" */
        if ($code !== 'Ok') {
            Log::info('OsrmRoutingProvider: no route found', ['code' => $code]);

            return RouteResult::notFound($origin, $destination, 'osrm');
        }

        $route = $data['routes'][0] ?? null;

        if ($route === null) {
            return RouteResult::notFound($origin, $destination, 'osrm');
        }

        $distanceMeters  = (float) ($route['distance'] ?? 0);
        $durationSeconds = (float) ($route['duration'] ?? 0);

        /* Geometría GeoJSON: [[lng, lat], [lng, lat], ...] */
        $geometry = $route['geometry']['coordinates'] ?? [];

        return new RouteResult(
            routeFound:         true,
            distanceMeters:     $distanceMeters,
            distanceKilometers: $distanceMeters / 1000,
            durationSeconds:    $durationSeconds,
            durationMinutes:    $durationSeconds / 60,
            geometry:           $geometry,
            origin:             $origin,
            destination:        $destination,
            provider:           'osrm',
        );
    }

    private function assertValidCoordinates(float $lat, float $lng): void
    {
        if ($lat < -90 || $lat > 90) {
            throw new RoutingException("Latitud fuera de rango: {$lat}. Debe estar entre -90 y 90.");
        }

        if ($lng < -180 || $lng > 180) {
            throw new RoutingException("Longitud fuera de rango: {$lng}. Debe estar entre -180 y 180.");
        }
    }
}
