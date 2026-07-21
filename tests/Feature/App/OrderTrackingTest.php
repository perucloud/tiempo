<?php

namespace Tests\Feature\App;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function makePedido(array $attrs = []): Pedido
    {
        $negocio = NegocioAfiliado::factory()->create();
        $cliente = Cliente::factory()->create(['telefono' => '999111222']);

        $pedido = Pedido::factory()->create(array_merge([
            'codigo'              => 'PED-20260720-00001',
            'negocio_afiliado_id' => $negocio->id,
            'cliente_id'          => $cliente->id,
            'estado'              => Pedido::ESTADO_PENDIENTE,
            'estado_pago'         => Pedido::PAGO_PENDIENTE,
            'subtotal'            => '15.00',
            'costo_delivery'      => '5.00',
            'total'               => '20.00',
        ], $attrs));

        session()->put('app_customer_phone', $cliente->telefono);
        session()->push('app_order_ids', $pedido->id);

        $this->actingAs($cliente, 'cliente');

        return $pedido;
    }

    /* ── show() ── */

    public function test_tracking_page_renders_for_valid_codigo(): void
    {
        $pedido = $this->makePedido();

        $response = $this->get(route('app.orders.show', $pedido->codigo));

        $response->assertOk()
                 ->assertSee($pedido->codigo);
    }

    public function test_tracking_page_redirects_to_home_for_unknown_codigo(): void
    {
        $response = $this->get(route('app.orders.show', 'PED-00000000-00000'));

        $response->assertRedirect(route('app.home'));
    }

    public function test_tracking_page_hides_order_from_another_session(): void
    {
        $pedido = $this->makePedido();
        session()->forget(['app_order_ids', 'app_customer_phone']);

        $this->get(route('app.orders.show', $pedido->codigo))
            ->assertRedirect(route('app.home'));
    }

    public function test_tracking_page_shows_order_error_flash_after_bad_redirect(): void
    {
        $response = $this->withSession(['order_error' => 'No encontramos el pedido XYZ.'])
                         ->get(route('app.home'));

        $response->assertOk()->assertSee('No encontramos el pedido XYZ.');
    }

    /* ── estado() ── */

    public function test_estado_endpoint_returns_json_with_estado(): void
    {
        $pedido = $this->makePedido(['estado' => Pedido::ESTADO_CONFIRMADO]);

        $response = $this->getJson(route('app.orders.estado', $pedido->codigo));

        $response->assertOk()
                 ->assertJsonFragment(['estado' => Pedido::ESTADO_CONFIRMADO]);
    }

    public function test_estado_endpoint_includes_label(): void
    {
        $pedido = $this->makePedido(['estado' => Pedido::ESTADO_PENDIENTE]);

        $response = $this->getJson(route('app.orders.estado', $pedido->codigo));

        $response->assertOk()
                 ->assertJsonStructure(['estado', 'estado_pago', 'label', 'repartidor']);
    }

    public function test_estado_endpoint_returns_404_for_unknown_codigo(): void
    {
        $this->getJson(route('app.orders.estado', 'PED-XXXX-00000'))
             ->assertNotFound();
    }

    /* ── buscarPorTelefono() ── */

    public function test_buscar_por_telefono_returns_pedidos_for_known_client(): void
    {
        $pedido = $this->makePedido();

        $response = $this->postJson(route('app.perfil.buscar'), ['telefono' => '999111222']);

        $response->assertOk()
                 ->assertJsonStructure(['pedidos' => [['codigo', 'negocio', 'total', 'estado', 'hace', 'url']]])
                 ->assertJsonFragment(['codigo' => $pedido->codigo]);
    }

    public function test_buscar_por_telefono_returns_empty_for_unknown_client(): void
    {
        $pedido = $this->makePedido();

        $response = $this->postJson(route('app.perfil.buscar'), ['telefono' => '000000000']);

        $response->assertOk()
                 ->assertJsonFragment(['pedidos' => []]);
    }

    public function test_buscar_por_telefono_does_not_expose_another_customer_history(): void
    {
        $this->makePedido();
        session()->forget(['app_order_ids', 'app_customer_phone']);

        $this->postJson(route('app.perfil.buscar'), ['telefono' => '999111222'])
            ->assertOk()
            ->assertJsonPath('pedidos', []);
    }

    public function test_buscar_por_telefono_validates_telefono_required(): void
    {
        $pedido = $this->makePedido();

        $this->postJson(route('app.perfil.buscar'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['telefono']);
    }

    public function test_buscar_por_telefono_limits_to_6_pedidos(): void
    {
        $negocio = NegocioAfiliado::factory()->create();
        $cliente = Cliente::factory()->create(['telefono' => '988777666']);
        Pedido::factory()->count(10)->create([
            'negocio_afiliado_id' => $negocio->id,
            'cliente_id'          => $cliente->id,
        ]);

        session()->put('app_customer_phone', $cliente->telefono);
        $this->actingAs($cliente, 'cliente');

        $response = $this->postJson(route('app.perfil.buscar'), ['telefono' => '988777666']);

        $response->assertOk();
        $this->assertCount(6, $response->json('pedidos'));
    }

    /* ── Pago upload (PaymentController) ── */

    public function test_payment_store_creates_pago_and_redirects_to_tracking(): void
    {
        $pedido = $this->makePedido();

        $response = $this->post(route('app.payments.store'), [
            'codigo'           => $pedido->codigo,
            'metodo'           => Pago::METODO_YAPE,
            'codigo_operacion' => '12345678',
        ]);

        $response->assertRedirect(route('app.orders.show', $pedido->codigo));
        $this->assertDatabaseHas('pagos', ['metodo' => Pago::METODO_YAPE]);
        $pedido->refresh();
        $this->assertEquals(Pedido::ESTADO_PAGO_EN_REVISION, $pedido->estado);
    }

    public function test_payment_store_rejects_invalid_metodo(): void
    {
        $pedido = $this->makePedido();

        $this->post(route('app.payments.store'), [
            'codigo' => $pedido->codigo,
            'metodo' => 'bitcoin',
        ])->assertSessionHasErrors(['metodo']);
    }

    public function test_payment_store_hides_order_from_another_session(): void
    {
        $pedido = $this->makePedido();
        session()->forget(['app_order_ids', 'app_customer_phone']);

        $this->post(route('app.payments.store'), [
            'codigo' => $pedido->codigo,
            'metodo' => Pago::METODO_YAPE,
        ])->assertNotFound();

        $this->assertDatabaseCount('pagos', 0);
    }

    public function test_payment_cannot_be_registered_twice(): void
    {
        $pedido = $this->makePedido();

        $payload = ['codigo' => $pedido->codigo, 'metodo' => Pago::METODO_YAPE];
        $this->post(route('app.payments.store'), $payload)->assertRedirect();
        $this->post(route('app.payments.store'), $payload)->assertRedirect();

        $this->assertDatabaseCount('pagos', 1);
    }
}
