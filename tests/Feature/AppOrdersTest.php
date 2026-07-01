<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_order_from_cart(): void
    {
        $product = Producto::factory()->create(['nombre' => 'Combo pollo', 'precio' => 30]);

        $this->post('/app/cart', ['product_id' => $product->id, 'quantity' => 2]);
        $this->patch('/app/cart/address', ['delivery_address' => 'Av. Principal 123']);

        $this->post('/app/orders', [
            'nombres' => 'Cliente',
            'telefono' => '999888777',
            'email' => 'cliente@tiempo.test',
            'notas' => 'Tocar puerta',
        ])->assertRedirect('/app#pedidos');

        $this->assertDatabaseHas('clientes', [
            'telefono' => '999888777',
            'nombres' => 'Cliente',
        ]);
        $this->assertDatabaseHas('pedidos', [
            'estado' => Pedido::ESTADO_PENDIENTE,
            'estado_pago' => Pedido::PAGO_PENDIENTE,
            'direccion_entrega' => 'Av. Principal 123',
            'subtotal' => 60,
            'costo_delivery' => 5,
            'total' => 65,
        ]);
        $this->assertDatabaseHas('pedido_detalles', [
            'producto_nombre' => 'Combo pollo',
            'cantidad' => 2,
            'precio_unitario' => 30,
            'subtotal' => 60,
        ]);
        $this->assertDatabaseHas('pedido_estados', [
            'estado_nuevo' => Pedido::ESTADO_PENDIENTE,
            'comentario' => 'Pedido creado desde la app.',
        ]);
        $this->assertNull(session('app_cart'));
    }

    public function test_order_requires_delivery_address(): void
    {
        $product = Producto::factory()->create();

        $this->post('/app/cart', ['product_id' => $product->id, 'quantity' => 1]);

        $this->post('/app/orders', [
            'nombres' => 'Cliente',
            'telefono' => '999888777',
        ])->assertRedirect('/app#checkout')
            ->assertSessionHasErrors('order');

        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_order_requires_non_empty_cart(): void
    {
        $this->patch('/app/cart/address', ['delivery_address' => 'Av. Principal 123']);

        $this->post('/app/orders', [
            'nombres' => 'Cliente',
            'telefono' => '999888777',
        ])->assertRedirect('/app#checkout')
            ->assertSessionHasErrors('order');

        $this->assertDatabaseCount('pedidos', 0);
    }
}
