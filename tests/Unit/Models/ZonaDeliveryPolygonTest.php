<?php

namespace Tests\Unit\Models;

use App\Models\ZonaDelivery;
use PHPUnit\Framework\TestCase;

class ZonaDeliveryPolygonTest extends TestCase
{
    private function makeZone(array $polygon): ZonaDelivery
    {
        $zone = new ZonaDelivery;
        // Forzar el atributo polygon sin pasar por el cast (TestCase sin DB)
        $zone->setRawAttributes(['polygon' => json_encode($polygon)]);

        return $zone;
    }

    /* ── tienePoligono() ── */

    public function test_tiene_poligono_false_when_null(): void
    {
        $zone = new ZonaDelivery;
        $zone->setRawAttributes(['polygon' => null]);
        $this->assertFalse($zone->tienePoligono());
    }

    public function test_tiene_poligono_false_when_less_than_3_vertices(): void
    {
        $zone = $this->makeZone([[-74.64, -11.25], [-74.63, -11.25]]);
        $this->assertFalse($zone->tienePoligono());
    }

    public function test_tiene_poligono_true_when_3_or_more(): void
    {
        $zone = $this->makeZone([
            [-74.65, -11.24],
            [-74.62, -11.24],
            [-74.62, -11.27],
        ]);
        $this->assertTrue($zone->tienePoligono());
    }

    /* ── containsPoint() — cuadrado simple centrado en Satipo ── */

    /**
     * Polígono: cuadrado en lng [-74.65, -74.62] × lat [-11.27, -11.24]
     * Vértices en GeoJSON [lng, lat]:
     *   (-74.65,-11.24) → (-74.62,-11.24) → (-74.62,-11.27) → (-74.65,-11.27)
     */
    private function squareZone(): ZonaDelivery
    {
        return $this->makeZone([
            [-74.65, -11.24],
            [-74.62, -11.24],
            [-74.62, -11.27],
            [-74.65, -11.27],
        ]);
    }

    public function test_point_inside_square_returns_true(): void
    {
        $zone = $this->squareZone();
        // Centro del cuadrado
        $this->assertTrue($zone->containsPoint(-11.255, -74.635));
    }

    public function test_point_outside_east_returns_false(): void
    {
        $zone = $this->squareZone();
        $this->assertFalse($zone->containsPoint(-11.255, -74.610));
    }

    public function test_point_outside_north_returns_false(): void
    {
        $zone = $this->squareZone();
        $this->assertFalse($zone->containsPoint(-11.230, -74.635));
    }

    public function test_point_outside_south_returns_false(): void
    {
        $zone = $this->squareZone();
        $this->assertFalse($zone->containsPoint(-11.280, -74.635));
    }

    public function test_point_outside_west_returns_false(): void
    {
        $zone = $this->squareZone();
        $this->assertFalse($zone->containsPoint(-11.255, -74.660));
    }

    public function test_contains_point_false_when_no_polygon(): void
    {
        $zone = new ZonaDelivery;
        $zone->setRawAttributes(['polygon' => null]);
        $this->assertFalse($zone->containsPoint(-11.255, -74.635));
    }

    public function test_contains_point_false_when_polygon_less_than_3(): void
    {
        $zone = $this->makeZone([[-74.65, -11.24], [-74.62, -11.24]]);
        $this->assertFalse($zone->containsPoint(-11.255, -74.635));
    }

    /* ── Polígono triangular ── */

    public function test_point_inside_triangle_returns_true(): void
    {
        // Triángulo: punta superior, base inferior izquierda y derecha
        $zone = $this->makeZone([
            [-74.635, -11.240],   // punta norte
            [-74.650, -11.270],   // base SO
            [-74.620, -11.270],   // base SE
        ]);
        // Centro del triángulo
        $this->assertTrue($zone->containsPoint(-11.260, -74.635));
    }

    public function test_point_above_triangle_returns_false(): void
    {
        $zone = $this->makeZone([
            [-74.635, -11.240],
            [-74.650, -11.270],
            [-74.620, -11.270],
        ]);
        $this->assertFalse($zone->containsPoint(-11.230, -74.635));
    }
}
