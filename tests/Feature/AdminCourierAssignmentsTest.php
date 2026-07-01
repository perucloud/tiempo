<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCourierAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_assign_courier_to_order(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $order = $this->makeOrder(['estado' => Pedido::ESTADO_LISTO]);
        $courier = Repartidor::factory()->create(['estado' => Repartidor::ESTADO_DISPONIBLE]);

        $this->actingAs($operator)
            ->patch("/admin/orders/{$order->id}/courier", [
                'repartidor_id' => $courier->id,
                'comentario' => 'Sale hacia el negocio afiliado.',
            ])
            ->assertRedirect("/admin/orders/{$order->id}/edit");

        $this->assertDatabaseHas('pedidos', [
            'id' => $order->id,
            'repartidor_id' => $courier->id,
            'estado' => Pedido::ESTADO_ASIGNADO,
        ]);
        $this->assertDatabaseHas('repartidores', [
            'id' => $courier->id,
            'estado' => Repartidor::ESTADO_OCUPADO,
        ]);
        $this->assertDatabaseHas('pedido_estados', [
            'pedido_id' => $order->id,
            'user_id' => $operator->id,
            'estado_anterior' => Pedido::ESTADO_LISTO,
            'estado_nuevo' => Pedido::ESTADO_ASIGNADO,
            'comentario' => 'Sale hacia el negocio afiliado.',
        ]);
    }

    public function test_operator_cannot_assign_inactive_courier(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $order = $this->makeOrder();
        $courier = Repartidor::factory()->create(['estado' => Repartidor::ESTADO_INACTIVO]);

        $this->actingAs($operator)
            ->from("/admin/orders/{$order->id}/edit")
            ->patch("/admin/orders/{$order->id}/courier", [
                'repartidor_id' => $courier->id,
            ])
            ->assertRedirect("/admin/orders/{$order->id}/edit")
            ->assertSessionHasErrors('repartidor_id');
    }

    public function test_delivered_order_releases_assigned_courier(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $courier = Repartidor::factory()->create(['estado' => Repartidor::ESTADO_OCUPADO]);
        $order = $this->makeOrder([
            'repartidor_id' => $courier->id,
            'estado' => Pedido::ESTADO_EN_CAMINO,
        ]);

        $this->actingAs($operator)
            ->put("/admin/orders/{$order->id}", [
                'estado' => Pedido::ESTADO_ENTREGADO,
                'comentario' => 'Pedido entregado.',
            ])
            ->assertRedirect("/admin/orders/{$order->id}/edit");

        $this->assertDatabaseHas('pedidos', [
            'id' => $order->id,
            'estado' => Pedido::ESTADO_ENTREGADO,
        ]);
        $this->assertDatabaseHas('repartidores', [
            'id' => $courier->id,
            'estado' => Repartidor::ESTADO_DISPONIBLE,
        ]);
    }

    private function makeOrder(array $attributes = []): Pedido
    {
        return Pedido::query()->create(array_merge([
            'codigo' => Pedido::nextCode(),
            'cliente_id' => Cliente::factory()->create()->id,
            'negocio_afiliado_id' => NegocioAfiliado::factory()->create()->id,
            'estado' => Pedido::ESTADO_PENDIENTE,
            'estado_pago' => Pedido::PAGO_PENDIENTE,
            'direccion_entrega' => 'Av. Principal 123',
            'subtotal' => 30,
            'costo_delivery' => 5,
            'total' => 35,
        ], $attributes));
    }

    private function makeUser(string $role): User
    {
        return User::query()->create([
            'name' => "Usuario {$role}",
            'email' => "{$role}@tiempo.test",
            'password' => Hash::make('secret-password'),
            'role' => $role,
            'status' => User::STATUS_ACTIVE,
        ]);
    }
}
