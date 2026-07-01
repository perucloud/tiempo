<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_orders_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $order = $this->makeOrder();

        $this->actingAs($operator)
            ->get('/admin/orders')
            ->assertOk()
            ->assertSee('Pedidos recibidos')
            ->assertSee($order->codigo)
            ->assertSee($order->cliente->telefono);
    }

    public function test_operator_can_update_order_status_and_history(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $order = $this->makeOrder();

        $this->actingAs($operator)
            ->put("/admin/orders/{$order->id}", [
                'estado' => Pedido::ESTADO_CONFIRMADO,
                'comentario' => 'Pago confirmado por operador.',
            ])
            ->assertRedirect("/admin/orders/{$order->id}/edit");

        $this->assertDatabaseHas('pedidos', [
            'id' => $order->id,
            'estado' => Pedido::ESTADO_CONFIRMADO,
        ]);
        $this->assertDatabaseHas('pedido_estados', [
            'pedido_id' => $order->id,
            'user_id' => $operator->id,
            'estado_anterior' => Pedido::ESTADO_PENDIENTE,
            'estado_nuevo' => Pedido::ESTADO_CONFIRMADO,
            'comentario' => 'Pago confirmado por operador.',
        ]);
    }

    public function test_affiliated_business_cannot_manage_global_orders(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/orders')
            ->assertForbidden();
    }

    public function test_orders_can_be_filtered_by_status(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $pending = $this->makeOrder(['codigo' => 'PED-PENDIENTE', 'estado' => Pedido::ESTADO_PENDIENTE]);
        $confirmed = $this->makeOrder(['codigo' => 'PED-CONFIRMADO', 'estado' => Pedido::ESTADO_CONFIRMADO]);

        $this->actingAs($admin)
            ->get('/admin/orders?estado=pendiente')
            ->assertOk()
            ->assertSee($pending->codigo)
            ->assertDontSee($confirmed->codigo);
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
