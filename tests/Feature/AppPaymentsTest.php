<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppPaymentsTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $authCliente;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authCliente = Cliente::factory()->create();
        $this->actingAs($this->authCliente, 'cliente');
    }

    public function test_customer_can_register_payment_for_order(): void
    {
        $order = $this->makeOrder();

        $this->withSession(['app_order_ids' => [$order->id]])
            ->post('/app/payments', [
                'codigo' => $order->codigo,
                'metodo' => Pago::METODO_YAPE,
                'codigo_operacion' => 'OP123',
                'voucher_path' => 'https://example.com/voucher.jpg',
            ])->assertRedirect(route('app.orders.show', $order->codigo));

        $this->assertDatabaseHas('pagos', [
            'pedido_id' => $order->id,
            'metodo' => Pago::METODO_YAPE,
            'monto' => $order->total,
            'estado' => Pago::ESTADO_PENDIENTE,
            'codigo_operacion' => 'OP123',
        ]);
        $this->assertDatabaseHas('pedidos', [
            'id' => $order->id,
            'estado' => Pedido::ESTADO_PAGO_EN_REVISION,
            'estado_pago' => Pedido::PAGO_EN_REVISION,
        ]);
    }

    public function test_payment_requires_existing_order_code(): void
    {
        $this->post('/app/payments', [
            'codigo' => 'PED-NO-EXISTE',
            'metodo' => Pago::METODO_PLIN,
        ])->assertSessionHasErrors('codigo');
    }

    private function makeOrder(): Pedido
    {
        return Pedido::query()->create([
            'codigo' => Pedido::nextCode(),
            'cliente_id' => Cliente::factory()->create()->id,
            'negocio_afiliado_id' => NegocioAfiliado::factory()->create()->id,
            'estado' => Pedido::ESTADO_PENDIENTE,
            'estado_pago' => Pedido::PAGO_PENDIENTE,
            'direccion_entrega' => 'Av. Principal 123',
            'subtotal' => 30,
            'costo_delivery' => 5,
            'total' => 35,
        ]);
    }
}
