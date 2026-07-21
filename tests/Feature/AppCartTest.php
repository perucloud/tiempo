<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppCartTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cliente = Cliente::factory()->create();
        $this->actingAs($this->cliente, 'cliente');
    }

    public function test_customer_can_add_product_to_cart(): void
    {
        $product = Producto::factory()->create(['nombre' => 'Combo familiar', 'precio' => 40]);

        $this->post('/app/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertRedirect('/app/inicio#carrito');

        $this->get('/app/inicio')
            ->assertOk()
            ->assertSee('2 productos')
            ->assertSee('Combo familiar')
            ->assertSee('S/ 80.00');
    }

    public function test_customer_can_update_product_quantity(): void
    {
        $product = Producto::factory()->create(['precio' => 12]);

        $this->post('/app/cart', ['product_id' => $product->id, 'quantity' => 1]);

        $this->patch('/app/cart', [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertRedirect('/app/inicio#carrito');

        $this->get('/app/inicio')
            ->assertSee('3 productos')
            ->assertSee('S/ 36.00');
    }

    public function test_cart_keeps_one_affiliated_business_at_a_time(): void
    {
        $firstBusiness = NegocioAfiliado::factory()->create();
        $secondBusiness = NegocioAfiliado::factory()->create();
        $firstProduct = Producto::factory()->create([
            'negocio_afiliado_id' => $firstBusiness->id,
            'nombre' => 'Producto primero',
        ]);
        $secondProduct = Producto::factory()->create([
            'negocio_afiliado_id' => $secondBusiness->id,
            'nombre' => 'Producto segundo',
        ]);

        $this->post('/app/cart', ['product_id' => $firstProduct->id, 'quantity' => 1]);
        $this->post('/app/cart', ['product_id' => $secondProduct->id, 'quantity' => 1]);

        $this->assertSame($secondBusiness->id, session('app_cart.business_id'));
        $this->assertArrayHasKey((string) $secondProduct->id, session('app_cart.items'));
        $this->assertArrayNotHasKey((string) $firstProduct->id, session('app_cart.items'));
    }

    public function test_customer_can_prepare_delivery_address(): void
    {
        $this->patch('/app/cart/address', [
            'delivery_address' => 'Av. Siempre Viva 123',
        ])->assertRedirect('/app/inicio#carrito');

        $this->get('/app/inicio')
            ->assertSee('Av. Siempre Viva 123');
    }

    public function test_customer_can_clear_cart(): void
    {
        $product = Producto::factory()->create();

        $this->post('/app/cart', ['product_id' => $product->id, 'quantity' => 1]);

        $this->delete('/app/cart')
            ->assertRedirect('/app/inicio');

        $this->get('/app/inicio')
            ->assertSee('0 productos')
            ->assertSee('Agrega productos para preparar tu pedido.');
    }

    public function test_unavailable_product_cannot_be_added_to_cart(): void
    {
        $product = Producto::factory()->create([
            'estado' => Producto::ESTADO_INACTIVO,
            'disponible' => false,
        ]);

        $this->post('/app/cart', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertNotFound();
    }

    public function test_app_lists_active_catalog_products(): void
    {
        $category = Categoria::factory()->create(['nombre' => 'Promos']);
        $business = NegocioAfiliado::factory()->create(['nombre_comercial' => 'Cafe Centro']);
        Producto::factory()->create([
            'nombre' => 'Cafe especial',
            'negocio_afiliado_id' => $business->id,
            'categoria_id' => $category->id,
        ]);

        $this->get('/app/inicio')
            ->assertOk()
            ->assertSee('Promos')
            ->assertSee('Cafe Centro')
            ->assertSee('Cafe especial');
    }
}
