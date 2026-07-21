<?php

namespace Tests\Unit\Geo;

use App\DTOs\Geo\RouteResult;
use PHPUnit\Framework\TestCase;

class RouteResultTest extends TestCase
{
    private function makeResult(): RouteResult
    {
        return new RouteResult(
            routeFound:         true,
            distanceMeters:     3456.7,
            distanceKilometers: 3.4567,
            durationSeconds:    720.0,
            durationMinutes:    12.0,
            geometry:           [[-74.6362, -11.2534], [-74.6300, -11.2597]],
            origin:             ['lat' => -11.2534, 'lng' => -74.6362],
            destination:        ['lat' => -11.2597, 'lng' => -74.6300],
            provider:           'osrm',
        );
    }

    public function test_to_array_has_all_required_keys(): void
    {
        $arr = $this->makeResult()->toArray();

        $expected = [
            'route_found', 'distance_meters', 'distance_kilometers',
            'duration_seconds', 'duration_minutes',
            'geometry', 'origin', 'destination', 'provider',
        ];
        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $arr, "Missing key: {$key}");
        }
    }

    public function test_to_array_preserves_raw_numeric_values(): void
    {
        $result = $this->makeResult();
        $arr    = $result->toArray();

        $this->assertSame(3456.7, $arr['distance_meters']);
        $this->assertSame(3.4567, $arr['distance_kilometers']);
        $this->assertSame(720.0,  $arr['duration_seconds']);
        $this->assertSame(12.0,   $arr['duration_minutes']);
    }

    public function test_to_array_does_not_round_values(): void
    {
        $result = new RouteResult(
            routeFound:         true,
            distanceMeters:     1234.56789,
            distanceKilometers: 1234.56789 / 1000,
            durationSeconds:    89.1,
            durationMinutes:    89.1 / 60,
            geometry:           [],
            origin:             ['lat' => 0.0, 'lng' => 0.0],
            destination:        ['lat' => 0.0, 'lng' => 0.0],
            provider:           'osrm',
        );
        $arr = $result->toArray();

        $this->assertSame(1234.56789, $arr['distance_meters']);
        $this->assertSame(89.1 / 60, $arr['duration_minutes']);
    }

    public function test_not_found_factory_sets_route_found_false(): void
    {
        $origin      = ['lat' => -11.25, 'lng' => -74.63];
        $destination = ['lat' => -11.26, 'lng' => -74.62];

        $result = RouteResult::notFound($origin, $destination, 'osrm');

        $this->assertFalse($result->routeFound);
        $this->assertSame(0.0, $result->distanceMeters);
        $this->assertSame(0.0, $result->durationSeconds);
        $this->assertSame([], $result->geometry);
        $this->assertSame('osrm', $result->provider);
    }

    public function test_geometry_preserves_lng_lat_order(): void
    {
        $result = $this->makeResult();

        /* OSRM devuelve [[lng, lat], ...], debe almacenarse sin modificar */
        $this->assertSame([-74.6362, -11.2534], $result->geometry[0]);
        $this->assertSame([-74.6300, -11.2597], $result->geometry[1]);
    }
}
