<?php

namespace Tests\Unit\Services;

use App\Contracts\Geo\RoutingProviderInterface;
use App\DTOs\Geo\RouteResult;
use App\Exceptions\Assignment\AlreadyAssignedException;
use App\Exceptions\Assignment\DriverNotAvailableException;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use App\Models\PedidoAsignacion;
use App\Models\Repartidor;
use App\Models\User;
use App\Services\DriverAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class DriverAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoutingProviderInterface $routing;
    private DriverAssignmentService  $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->routing = Mockery::mock(RoutingProviderInterface::class);
        $this->svc     = new DriverAssignmentService($this->routing);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /* ── helpers ── */

    private function makeUser(string $role = User::ROLE_ADMIN): User
    {
        return User::factory()->create(['role' => $role, 'status' => 'activo']);
    }

    private function makeNegocio(float $lat = -11.2534, float $lng = -74.6362): NegocioAfiliado
    {
        $user = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        return NegocioAfiliado::factory()->create([
            'user_id'  => $user->id,
            'latitud'  => $lat,
            'longitud' => $lng,
        ]);
    }

    private function makeRepartidor(string $estado = 'disponible', ?float $lat = -11.250, ?float $lng = -74.630): Repartidor
    {
        $user = $this->makeUser(User::ROLE_REPARTIDOR);

        return Repartidor::factory()->create([
            'user_id'                   => $user->id,
            'estado'                    => $estado,
            'estado_operativo'          => $estado === 'disponible' ? Repartidor::OP_AVAILABLE : Repartidor::OP_OFFLINE,
            'latitud_actual'            => $lat,
            'longitud_actual'           => $lng,
            'ubicacion_actualizada_at'  => now(),
        ]);
    }

    private function makePedido(NegocioAfiliado $negocio, ?Repartidor $repartidor = null): Pedido
    {
        $cliente = \App\Models\Cliente::factory()->create();

        return Pedido::factory()->create([
            'negocio_afiliado_id' => $negocio->id,
            'cliente_id'          => $cliente->id,
            'repartidor_id'       => $repartidor?->id,
            'estado'              => Pedido::ESTADO_CONFIRMADO,
            'latitud_cliente'     => -11.260,
            'longitud_cliente'    => -74.640,
        ]);
    }

    private function mockRouteOK(float $distKm = 2.5, float $durMin = 10.0): void
    {
        $this->routing->shouldReceive('route')
            ->andReturn(new RouteResult(
                routeFound: true,
                distanceMeters: $distKm * 1000,
                distanceKilometers: $distKm,
                durationSeconds: $durMin * 60,
                durationMinutes: $durMin,
                geometry: [[-74.636, -11.253], [-74.640, -11.260]],
                origin: ['lat' => -11.250, 'lng' => -74.630],
                destination: ['lat' => -11.260, 'lng' => -74.640],
                provider: 'osrm',
                rawData: null,
            ));
    }

    /* ── assign: flujo exitoso ── */

    public function test_assign_creates_asignacion_record(): void
    {
        $negocio    = $this->makeNegocio();
        $repartidor = $this->makeRepartidor();
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->mockRouteOK();

        $asignacion = $this->svc->assign($pedido, $repartidor, $admin);

        $this->assertDatabaseHas('pedido_asignaciones', [
            'pedido_id'       => $pedido->id,
            'repartidor_id'   => $repartidor->id,
            'assignment_type' => PedidoAsignacion::TYPE_MANUAL,
            'status'          => PedidoAsignacion::STATUS_ACTIVO,
        ]);
        $this->assertInstanceOf(PedidoAsignacion::class, $asignacion);
    }

    public function test_assign_updates_pedido_state_to_asignado(): void
    {
        $negocio    = $this->makeNegocio();
        $repartidor = $this->makeRepartidor();
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->mockRouteOK();
        $this->svc->assign($pedido, $repartidor, $admin);

        $this->assertSame(Pedido::ESTADO_ASIGNADO, $pedido->fresh()->estado);
        $this->assertSame($repartidor->id, $pedido->fresh()->repartidor_id);
    }

    public function test_assign_sets_driver_to_ocupado_and_assigned_operativo(): void
    {
        $negocio    = $this->makeNegocio();
        $repartidor = $this->makeRepartidor();
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->mockRouteOK();
        $this->svc->assign($pedido, $repartidor, $admin);

        $fresh = $repartidor->fresh();
        $this->assertSame(Repartidor::ESTADO_OCUPADO,  $fresh->estado);
        $this->assertSame(Repartidor::OP_ASSIGNED, $fresh->estado_operativo);
    }

    public function test_assign_stores_route_geometry(): void
    {
        $negocio    = $this->makeNegocio();
        $repartidor = $this->makeRepartidor();
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->mockRouteOK(2.5, 10.0);
        $asignacion = $this->svc->assign($pedido, $repartidor, $admin);

        $this->assertNotNull($asignacion->route_to_business);
        $this->assertIsArray($asignacion->route_to_business);
    }

    /* ── assign: doble asignación ── */

    public function test_assign_throws_when_pedido_already_assigned(): void
    {
        $negocio    = $this->makeNegocio();
        $rep1       = $this->makeRepartidor();
        $rep2       = $this->makeRepartidor();
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->mockRouteOK();

        /* Primera asignación */
        $this->svc->assign($pedido, $rep1, $admin);

        /* Intentar segunda asignación sobre el mismo pedido */
        $this->expectException(AlreadyAssignedException::class);
        $this->svc->assign($pedido, $rep2, $admin);
    }

    /* ── assign: repartidor no disponible ── */

    public function test_assign_throws_when_driver_not_disponible(): void
    {
        $negocio    = $this->makeNegocio();
        $repartidor = $this->makeRepartidor('ocupado');
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->expectException(DriverNotAvailableException::class);
        $this->svc->assign($pedido, $repartidor, $admin);
    }

    /* ── cancelAssignment ── */

    public function test_cancel_assignment_frees_driver(): void
    {
        $negocio    = $this->makeNegocio();
        $repartidor = $this->makeRepartidor();
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->mockRouteOK();
        $asignacion = $this->svc->assign($pedido, $repartidor, $admin);

        $this->svc->cancelAssignment($asignacion);

        $this->assertSame(PedidoAsignacion::STATUS_CANCELADO, $asignacion->fresh()->status);
        $this->assertSame(Repartidor::ESTADO_DISPONIBLE, $repartidor->fresh()->estado);
        $this->assertSame(Repartidor::OP_AVAILABLE, $repartidor->fresh()->estado_operativo);
    }

    /* ── completeAssignment ── */

    public function test_complete_assignment_marks_completed_and_frees_driver(): void
    {
        $negocio    = $this->makeNegocio();
        $repartidor = $this->makeRepartidor();
        $pedido     = $this->makePedido($negocio);
        $admin      = $this->makeUser();

        $this->mockRouteOK();
        $asignacion = $this->svc->assign($pedido, $repartidor, $admin);

        $this->svc->completeAssignment($asignacion);

        $this->assertSame(PedidoAsignacion::STATUS_COMPLETADO, $asignacion->fresh()->status);
        $this->assertNotNull($asignacion->fresh()->completed_at);
        $this->assertSame(Repartidor::ESTADO_DISPONIBLE, $repartidor->fresh()->estado);
    }

    /* ── puedeTransicionar ── */

    public function test_driver_cannot_transition_invalid_state(): void
    {
        $rep = $this->makeRepartidor();
        $rep->estado_operativo = Repartidor::OP_ASSIGNED;

        $this->assertFalse($rep->puedeTransicionar(Repartidor::OP_DELIVERED));
        $this->assertTrue($rep->puedeTransicionar(Repartidor::OP_GOING_TO_BUSINESS));
    }

    public function test_full_state_chain_transitions(): void
    {
        $chain = [
            Repartidor::OP_ASSIGNED           => Repartidor::OP_GOING_TO_BUSINESS,
            Repartidor::OP_GOING_TO_BUSINESS  => Repartidor::OP_AT_BUSINESS,
            Repartidor::OP_AT_BUSINESS        => Repartidor::OP_PICKED_UP,
            Repartidor::OP_PICKED_UP          => Repartidor::OP_GOING_TO_CUSTOMER,
            Repartidor::OP_GOING_TO_CUSTOMER  => Repartidor::OP_DELIVERED,
            Repartidor::OP_DELIVERED          => Repartidor::OP_AVAILABLE,
        ];

        $rep = $this->makeRepartidor();
        foreach ($chain as $from => $to) {
            $rep->estado_operativo = $from;
            $this->assertTrue(
                $rep->puedeTransicionar($to),
                "Debe permitir transición {$from} → {$to}",
            );
        }
    }
}
