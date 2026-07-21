<?php

namespace Tests\Unit\Services;

use App\Contracts\Geo\RoutingProviderInterface;
use App\DTOs\Geo\RouteResult;
use App\Models\NegocioAfiliado;
use App\Models\NegocioDeliveryConfig;
use App\Models\ZonaDelivery;
use App\Services\DeliveryPricingService;
use Mockery;
use Tests\TestCase;

class DeliveryPricingServiceTest extends TestCase
{
    private RoutingProviderInterface $routing;
    private DeliveryPricingService   $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->routing = Mockery::mock(RoutingProviderInterface::class);
        $this->svc     = new DeliveryPricingService($this->routing);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /* ── helpers ── */

    private function makeNegocio(array $attrs = []): NegocioAfiliado
    {
        $n = new NegocioAfiliado;
        $n->setRawAttributes(array_merge([
            'latitud'            => '-11.2534',
            'longitud'           => '-74.6362',
            'tiempo_preparacion' => 20,
        ], $attrs));

        return $n;
    }

    private function makeZone(array $attrs = []): ZonaDelivery
    {
        $zone = new ZonaDelivery;
        $defaults = [
            'id'                   => 1,
            'nombre'               => 'Centro',
            'costo_delivery'       => '5.00',
            'km_incluidos'         => '0.00',
            'precio_por_km_extra'  => '0.00',
            'recargo'              => '0.00',
            'delivery_gratis_desde'=> null,
            'distancia_maxima_km'  => null,
            'activo'               => 1,
            'polygon'              => json_encode([
                [-74.65, -11.24], [-74.62, -11.24],
                [-74.62, -11.27], [-74.65, -11.27],
            ]),
        ];
        $zone->setRawAttributes(array_merge($defaults, $attrs));

        return $zone;
    }

    private function mockRoute(float $distKm = 3.0, float $durMin = 12.0): void
    {
        $this->routing->shouldReceive('route')
            ->once()
            ->andReturn(new RouteResult(
                routeFound: true,
                distanceMeters: $distKm * 1000,
                distanceKilometers: $distKm,
                durationSeconds: $durMin * 60,
                durationMinutes: $durMin,
                geometry: [],
                origin: ['lat' => -11.2534, 'lng' => -74.6362],
                destination: ['lat' => -11.255, 'lng' => -74.635],
                provider: 'osrm',
                rawData: null,
            ));
    }

    /* ── Paso 1: permite_delivery = false ── */

    public function test_unavailable_when_delivery_disabled(): void
    {
        $negocio = $this->makeNegocio();
        $config  = new NegocioDeliveryConfig;
        $config->setRawAttributes(['permite_delivery' => 0]);
        $negocio->setRelation('deliveryConfig', $config);

        $result = $this->svc->calculate($negocio, -11.255, -74.635);

        $this->assertFalse($result->available);
        $this->assertStringContainsString('no realiza delivery', $result->unavailableReason);
    }

    /* ── Paso 2: sin coordenadas ── */

    public function test_unavailable_when_negocio_has_no_coordinates(): void
    {
        $negocio = $this->makeNegocio(['latitud' => null, 'longitud' => null]);
        $negocio->setRelation('deliveryConfig', null);

        $result = $this->svc->calculate($negocio, -11.255, -74.635);

        $this->assertFalse($result->available);
        $this->assertStringContainsString('no tiene ubicación', $result->unavailableReason);
    }

    /* ── Paso 3: cliente fuera de zona ── */

    public function test_unavailable_when_client_out_of_coverage(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);

        // Subclase que devuelve null en findZoneForPoint (sin DB)
        $routing = $this->routing;
        $svc = new class($routing) extends DeliveryPricingService {
            public function __construct(RoutingProviderInterface $routing)
            {
                parent::__construct($routing);
            }
            public function findZoneForPoint(float $lat, float $lng): ?ZonaDelivery
            {
                return null;
            }
        };

        $result = $svc->calculate($negocio, 0.0, 0.0);

        $this->assertFalse($result->available);
        $this->assertStringContainsString('fuera del área', $result->unavailableReason);
    }

    /* ── Paso 4: OSRM no encuentra ruta ── */

    public function test_unavailable_when_route_not_found(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);

        $this->routing->shouldReceive('route')
            ->once()
            ->andReturn(RouteResult::notFound(
                ['lat' => -11.2534, 'lng' => -74.6362],
                ['lat' => -11.255,  'lng' => -74.635],
                'osrm',
            ));

        // Inyectar zona directamente para bypassear findZoneForPoint (que usa DB)
        $zone = $this->makeZone();
        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);

        $this->assertFalse($result->available);
        $this->assertStringContainsString('No se encontró ruta', $result->unavailableReason);
    }

    /* ── Paso 6: precio base de zona ── */

    public function test_base_price_from_zone(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone(['costo_delivery' => '5.00']);

        $this->mockRoute(2.0);

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);

        $this->assertTrue($result->available);
        $this->assertSame('5.00', $result->basePrice);
        $this->assertSame('5.00', $result->finalDeliveryPrice);
    }

    /* ── Paso 7: km extra ── */

    public function test_extra_km_cost_added_correctly(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone([
            'costo_delivery'      => '5.00',
            'km_incluidos'        => '2.00',
            'precio_por_km_extra' => '1.50',
        ]);

        $this->mockRoute(4.0);    // 4 km → 2 km extra → S/3.00

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);

        $this->assertTrue($result->available);
        $this->assertSame('3.00', $result->extraKilometerCost);
        $this->assertSame('8.00', $result->finalDeliveryPrice);
    }

    /* ── Paso 8: recargo de zona ── */

    public function test_zone_surcharge_added(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone([
            'costo_delivery' => '5.00',
            'recargo'        => '2.00',
        ]);

        $this->mockRoute(2.0);

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);

        $this->assertSame('2.00', $result->zoneSurcharge);
        $this->assertSame('7.00', $result->finalDeliveryPrice);
    }

    /* ── Paso 9: delivery gratis ── */

    public function test_free_delivery_when_subtotal_exceeds_threshold(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone([
            'costo_delivery'        => '5.00',
            'delivery_gratis_desde' => '50.00',
        ]);

        $this->mockRoute(2.0);

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '60.00', $zone);

        $this->assertSame('0.00', $result->finalDeliveryPrice);
        $this->assertSame('5.00', $result->discounts);
    }

    public function test_no_free_delivery_when_subtotal_below_threshold(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone([
            'costo_delivery'        => '5.00',
            'delivery_gratis_desde' => '50.00',
        ]);

        $this->mockRoute(2.0);

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '30.00', $zone);

        $this->assertSame('5.00', $result->finalDeliveryPrice);
        $this->assertSame('0.00', $result->discounts);
    }

    /* ── Paso 5: distancia máxima excedida ── */

    public function test_unavailable_when_distance_exceeds_max(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone(['distancia_maxima_km' => '3.00']);

        $this->mockRoute(5.0);    // excede 3 km

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);

        $this->assertFalse($result->available);
        $this->assertStringContainsString('distancia máxima', $result->unavailableReason);
    }

    /* ── Paso 10: tiempo estimado total ── */

    public function test_estimated_total_time_includes_preparation(): void
    {
        $negocio = $this->makeNegocio(['tiempo_preparacion' => 25]);
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone(['costo_delivery' => '5.00']);

        $this->mockRoute(2.0, 10.0);   // 10 min en ruta

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);

        $this->assertSame(25, $result->preparationMinutes);
        $this->assertEqualsWithDelta(35.0, $result->estimatedTotalMinutes, 0.01);
    }

    /* ── Config negocio override precio base ── */

    public function test_negocio_config_overrides_base_price(): void
    {
        $negocio = $this->makeNegocio();
        $config  = new NegocioDeliveryConfig;
        $config->setRawAttributes([
            'permite_delivery'  => 1,
            'precio_base_custom'=> '3.50',
        ]);
        $negocio->setRelation('deliveryConfig', $config);

        $zone = $this->makeZone(['costo_delivery' => '5.00']);

        $this->mockRoute(2.0);

        $result = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);

        $this->assertSame('3.50', $result->basePrice);
        $this->assertSame('3.50', $result->finalDeliveryPrice);
    }

    /* ── pricingSnapshot ── */

    public function test_pricing_snapshot_has_required_keys(): void
    {
        $negocio = $this->makeNegocio();
        $negocio->setRelation('deliveryConfig', null);
        $zone = $this->makeZone(['costo_delivery' => '5.00']);

        $this->mockRoute(2.0);

        $result   = $this->invokeCalculateWithZone($negocio, -11.255, -74.635, '0.00', $zone);
        $snapshot = $result->pricingSnapshot();

        $this->assertArrayHasKey('zone_id',               $snapshot);
        $this->assertArrayHasKey('final_delivery_price',  $snapshot);
        $this->assertArrayHasKey('calculated_at',         $snapshot);
        $this->assertArrayHasKey('distance_km',           $snapshot);
    }

    /**
     * Invoca DeliveryPricingService::calculate() inyectando una zona concreta
     * en lugar de depender de la base de datos (findZoneForPoint usa DB).
     * Usa un servicio parcialmente sustituido mediante subclase anónima.
     */
    private function invokeCalculateWithZone(
        NegocioAfiliado $negocio,
        float $lat,
        float $lng,
        string $subtotal,
        ZonaDelivery $zone,
    ) {
        $routing = $this->routing;
        $svc     = new class($routing, $zone) extends DeliveryPricingService {
            public function __construct(
                RoutingProviderInterface $routing,
                private readonly ZonaDelivery $injectedZone,
            ) {
                parent::__construct($routing);
            }

            public function findZoneForPoint(float $lat, float $lng): ?ZonaDelivery
            {
                return $this->injectedZone;
            }
        };

        return $svc->calculate($negocio, $lat, $lng, $subtotal);
    }
}
