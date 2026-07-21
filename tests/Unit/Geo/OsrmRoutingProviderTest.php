<?php

namespace Tests\Unit\Geo;

use App\DTOs\Geo\RouteResult;
use App\Exceptions\Geo\RoutingException;
use App\Services\Geo\OsrmRoutingProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OsrmRoutingProviderTest extends TestCase
{
    /* ── Fixture: respuesta OSRM exitosa ────────────────────── */
    private function osrmSuccessFixture(): array
    {
        return [
            'code'   => 'Ok',
            'routes' => [[
                'distance' => 3456.7,
                'duration' => 720.0,
                'geometry' => [
                    'type'        => 'LineString',
                    'coordinates' => [
                        [-74.6362, -11.2534],
                        [-74.6340, -11.2550],
                        [-74.6300, -11.2597],
                    ],
                ],
                'legs' => [],
            ]],
            'waypoints' => [],
        ];
    }

    /* ── Fixture: OSRM sin ruta ─────────────────────────────── */
    private function osrmNoRouteFixture(): array
    {
        return ['code' => 'NoRoute', 'message' => 'No route found'];
    }

    public function test_route_returns_route_result_on_success(): void
    {
        Http::fake(['*/route/*' => Http::response($this->osrmSuccessFixture(), 200)]);

        $result = (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);

        $this->assertInstanceOf(RouteResult::class, $result);
        $this->assertTrue($result->routeFound);
        $this->assertSame(3456.7, $result->distanceMeters);
        $this->assertSame(3456.7 / 1000, $result->distanceKilometers);
        $this->assertSame(720.0, $result->durationSeconds);
        $this->assertSame(720.0 / 60, $result->durationMinutes);
        $this->assertCount(3, $result->geometry);
        $this->assertSame('osrm', $result->provider);
    }

    public function test_route_geometry_preserves_lng_lat_order(): void
    {
        Http::fake(['*/route/*' => Http::response($this->osrmSuccessFixture(), 200)]);

        $result = (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);

        /* Primer punto: [lng=-74.6362, lat=-11.2534] — orden GeoJSON */
        $this->assertSame([-74.6362, -11.2534], $result->geometry[0]);
    }

    public function test_route_returns_not_found_when_osrm_code_is_no_route(): void
    {
        Http::fake(['*/route/*' => Http::response($this->osrmNoRouteFixture(), 200)]);

        $result = (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);

        $this->assertFalse($result->routeFound);
        $this->assertSame(0.0, $result->distanceMeters);
    }

    public function test_route_throws_routing_exception_on_http_error(): void
    {
        Http::fake(['*/route/*' => Http::response([], 503)]);

        $this->expectException(RoutingException::class);
        $this->expectExceptionMessageMatches('/503/');

        (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);
    }

    public function test_route_throws_routing_exception_on_connection_failure(): void
    {
        Http::fake(['*/route/*' => function () {
            throw new ConnectionException('timed out');
        }]);

        $this->expectException(RoutingException::class);

        (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);
    }

    public function test_route_throws_on_invalid_latitude(): void
    {
        $this->expectException(RoutingException::class);
        $this->expectExceptionMessageMatches('/Latitud/');

        (new OsrmRoutingProvider())->route(91.0, -74.6362, -11.2597, -74.6300);
    }

    public function test_route_throws_on_invalid_longitude(): void
    {
        $this->expectException(RoutingException::class);
        $this->expectExceptionMessageMatches('/Longitud/');

        (new OsrmRoutingProvider())->route(-11.2534, 181.0, -11.2597, -74.6300);
    }

    public function test_route_url_includes_lng_lat_order_not_lat_lng(): void
    {
        Http::fake(['*/route/*' => Http::response($this->osrmSuccessFixture(), 200)]);

        (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);

        /* OSRM espera lng,lat — verificar que la URL tiene -74.6362,-11.2534 */
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '-74.6362,-11.2534');
        });
    }

    public function test_route_uses_base_url_from_config(): void
    {
        config(['geo.routing.base_url' => 'https://osrm.custom.example.com']);
        Http::fake(['https://osrm.custom.example.com/*' => Http::response($this->osrmSuccessFixture(), 200)]);

        $result = (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);

        $this->assertTrue($result->routeFound);
        Http::assertSentCount(1);
    }

    public function test_route_uses_profile_from_config(): void
    {
        config(['geo.routing.profile' => 'bicycle']);
        Http::fake(['*/route/v1/bicycle/*' => Http::response($this->osrmSuccessFixture(), 200)]);

        (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);

        Http::assertSent(fn ($r) => str_contains($r->url(), '/route/v1/bicycle/'));
    }

    public function test_origin_and_destination_stored_correctly(): void
    {
        Http::fake(['*/route/*' => Http::response($this->osrmSuccessFixture(), 200)]);

        $result = (new OsrmRoutingProvider())->route(-11.2534, -74.6362, -11.2597, -74.6300);

        $this->assertSame(['lat' => -11.2534, 'lng' => -74.6362], $result->origin);
        $this->assertSame(['lat' => -11.2597, 'lng' => -74.6300], $result->destination);
    }
}
