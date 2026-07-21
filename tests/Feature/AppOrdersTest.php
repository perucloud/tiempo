<?php

namespace Tests\Feature;

use App\Contracts\Geo\RoutingProviderInterface;
use App\DTOs\Geo\RouteResult;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ZonaDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppOrdersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(RoutingProviderInterface::class, new class implements RoutingProviderInterface {
            public function route(float $originLat, float $originLng, float $destinationLat, float $destinationLng): RouteResult
            {
                return new RouteResult(true, 3000, 3, 720, 12, [],
                    ['lat' => $originLat, 'lng' => $originLng],
                    ['lat' => $destinationLat, 'lng' => $destinationLng], 'fake', null);
            }
        });

        ZonaDelivery::query()->create([
            'nombre' => 'Centro',
            'polygon' => [[-74.65, -11.24], [-74.62, -11.24], [-74.62, -11.27], [-74.65, -11.27]],
            'costo_delivery' => 5,
            'km_incluidos' => 3,
            'precio_por_km_extra' => 1,
            'prioridad' => 1,
            'activo' => true,
        ]);
    }

    public function test_customer_can_create_order_from_cart(): void
    {
        $product = Producto::factory()->create(['nombre' => 'Combo pollo', 'precio' => 30]);
        $product->negocioAfiliado()->update(['latitud' => -11.2534, 'longitud' => -74.6362]);

        $this->post('/app/cart', ['product_id' => $product->id, 'quantity' => 2]);
        $this->patch('/app/cart/address', ['delivery_address' => 'Av. Principal 123']);

        $response = $this->post('/app/orders', [
            'nombres' => 'Cliente',
            'telefono' => '999888777',
            'email' => 'cliente@tiempo.test',
            'notas' => 'Tocar puerta',
            'latitud_cliente' => -11.255,
            'longitud_cliente' => -74.635,
        ]);

        $pedido = Pedido::query()->sole();
        $response->assertRedirect(route('app.orders.show', $pedido->codigo));

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
            'zona_delivery_id' => ZonaDelivery::query()->value('id'),
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
        $this->assertNotNull($pedido->delivery_pricing_snapshot);
    }

    public function test_order_is_rejected_outside_delivery_coverage(): void
    {
        $product = Producto::factory()->create();
        $product->negocioAfiliado()->update(['latitud' => -11.2534, 'longitud' => -74.6362]);
        $this->post('/app/cart', ['product_id' => $product->id]);
        $this->patch('/app/cart/address', ['delivery_address' => 'Fuera de zona']);

        $this->post('/app/orders', [
            'nombres' => 'Cliente',
            'telefono' => '999888777',
            'latitud_cliente' => -12.10,
            'longitud_cliente' => -77.03,
        ])->assertRedirect('/app#checkout')->assertSessionHasErrors('order');

        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_delivery_quote_returns_real_price_for_cart(): void
    {
        $product = Producto::factory()->create(['precio' => 20]);
        $product->negocioAfiliado()->update(['latitud' => -11.2534, 'longitud' => -74.6362]);
        $this->post('/app/cart', ['product_id' => $product->id]);

        $this->postJson(route('app.delivery.quote'), [
            'latitud' => -11.255,
            'longitud' => -74.635,
        ])->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('final_delivery_price', '5.00')
            ->assertJsonPath('order_total', '25.00');
    }

    public function test_order_requires_delivery_address(): void
    {
        $product = Producto::factory()->create();

        $this->post('/app/cart', ['product_id' => $product->id, 'quantity' => 1]);

        $this->post('/app/orders', [
            'nombres' => 'Cliente',
            'telefono' => '999888777',
            'latitud_cliente' => -11.255,
            'longitud_cliente' => -74.635,
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
            'latitud_cliente' => -11.255,
            'longitud_cliente' => -74.635,
        ])->assertRedirect('/app#checkout')
            ->assertSessionHasErrors('order');

        $this->assertDatabaseCount('pedidos', 0);
    }
}
