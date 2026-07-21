<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use App\Models\PedidoAsignacion;
use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CourierFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_courier_routes_require_authentication_and_ownership(): void
    {
        [$user, $courier] = $this->makeCourier();
        [, $other] = $this->makeCourier('other@tiempo.test');

        $this->get(route('courier.turno', $courier))->assertRedirect(route('courier.login'));
        $this->actingAs($user)->get(route('courier.turno', $other))->assertForbidden();
        $this->actingAs($user)->get(route('courier.turno', $courier))->assertOk();
    }

    public function test_courier_can_login_through_own_portal(): void
    {
        [, $courier] = $this->makeCourier();

        $this->post(route('courier.login.store'), [
            'email' => 'courier@tiempo.test',
            'password' => 'secret-password',
        ])->assertRedirect(route('courier.turno', $courier));
    }

    public function test_courier_transitions_update_order_and_complete_assignment(): void
    {
        [$user, $courier] = $this->makeCourier();
        $courier->update(['estado' => Repartidor::ESTADO_OCUPADO, 'estado_operativo' => Repartidor::OP_PICKED_UP]);
        $order = Pedido::factory()->create([
            'cliente_id' => Cliente::factory()->create()->id,
            'negocio_afiliado_id' => NegocioAfiliado::factory()->create()->id,
            'repartidor_id' => $courier->id,
            'estado' => Pedido::ESTADO_ASIGNADO,
        ]);
        $assignment = PedidoAsignacion::query()->create([
            'pedido_id' => $order->id,
            'repartidor_id' => $courier->id,
            'assigned_by' => User::factory()->create()->id,
            'assignment_type' => PedidoAsignacion::TYPE_MANUAL,
            'status' => PedidoAsignacion::STATUS_ACTIVO,
            'assigned_at' => now(),
        ]);

        $this->actingAs($user)->postJson(route('courier.estado.update', $courier), [
            'estado_operativo' => Repartidor::OP_GOING_TO_CUSTOMER,
        ])->assertOk();
        $this->assertDatabaseHas('pedidos', ['id' => $order->id, 'estado' => Pedido::ESTADO_EN_CAMINO]);

        $this->actingAs($user)->postJson(route('courier.estado.update', $courier), [
            'estado_operativo' => Repartidor::OP_DELIVERED,
        ])->assertOk();

        $this->assertDatabaseHas('pedidos', ['id' => $order->id, 'estado' => Pedido::ESTADO_ENTREGADO]);
        $this->assertDatabaseHas('pedido_asignaciones', ['id' => $assignment->id, 'status' => PedidoAsignacion::STATUS_COMPLETADO]);
        $this->assertDatabaseHas('repartidores', ['id' => $courier->id, 'estado' => Repartidor::ESTADO_DISPONIBLE]);
    }

    private function makeCourier(string $email = 'courier@tiempo.test'): array
    {
        $user = User::query()->create([
            'name' => 'Repartidor', 'email' => $email,
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_REPARTIDOR, 'status' => User::STATUS_ACTIVE,
        ]);
        $courier = Repartidor::factory()->create(['user_id' => $user->id]);

        return [$user, $courier];
    }
}
