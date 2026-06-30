<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\PedidoEstado;
use App\Models\Producto;
use App\Models\Repartidor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_tiempo_tables_exist(): void
    {
        foreach ([
            'roles',
            'permissions',
            'role_permissions',
            'categorias',
            'negocios_afiliados',
            'productos',
            'clientes',
            'repartidores',
            'pedidos',
            'pedido_detalles',
            'pagos',
            'pedido_estados',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}");
        }
    }

    public function test_order_domain_relationships_can_be_created(): void
    {
        $categoria = Categoria::factory()->create();
        $negocio = NegocioAfiliado::factory()->create();
        $producto = Producto::factory()->for($categoria)->for($negocio)->create();
        $cliente = Cliente::factory()->create();
        $repartidor = Repartidor::factory()->create();

        $pedido = Pedido::factory()
            ->for($cliente)
            ->for($negocio)
            ->for($repartidor)
            ->create();

        PedidoDetalle::query()->create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'cantidad' => 2,
            'precio_unitario' => 15,
            'subtotal' => 30,
        ]);

        Pago::query()->create([
            'pedido_id' => $pedido->id,
            'metodo' => 'yape',
            'monto' => 30,
            'estado' => Pago::ESTADO_PENDIENTE,
        ]);

        PedidoEstado::query()->create([
            'pedido_id' => $pedido->id,
            'estado_nuevo' => Pedido::ESTADO_PENDIENTE,
            'comentario' => 'Pedido creado para prueba.',
        ]);

        $pedido->load(['cliente', 'negocioAfiliado', 'repartidor', 'detalles', 'pagos', 'estados']);

        $this->assertTrue($pedido->cliente->is($cliente));
        $this->assertTrue($pedido->negocioAfiliado->is($negocio));
        $this->assertTrue($pedido->repartidor->is($repartidor));
        $this->assertCount(1, $pedido->detalles);
        $this->assertCount(1, $pedido->pagos);
        $this->assertCount(1, $pedido->estados);
    }
}
