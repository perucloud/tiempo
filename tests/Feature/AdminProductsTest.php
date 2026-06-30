<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\NegocioAfiliado;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_products_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $product = Producto::factory()->create(['nombre' => 'Pollo a la brasa']);

        $this->actingAs($operator)
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('Productos')
            ->assertSee('Pollo a la brasa')
            ->assertSee($product->negocioAfiliado->nombre_comercial);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $business = NegocioAfiliado::factory()->create();
        $category = Categoria::factory()->create();

        $this->actingAs($admin)
            ->post('/admin/products', [
                'negocio_afiliado_id' => $business->id,
                'categoria_id' => $category->id,
                'nombre' => 'Pizza americana',
                'descripcion' => 'Pizza familiar',
                'precio' => '35.90',
                'precio_promocional' => '29.90',
                'imagen' => 'https://example.com/pizza.jpg',
                'estado' => Producto::ESTADO_ACTIVO,
                'disponible' => '1',
            ])
            ->assertRedirect('/admin/products');

        $this->assertDatabaseHas('productos', [
            'negocio_afiliado_id' => $business->id,
            'categoria_id' => $category->id,
            'nombre' => 'Pizza americana',
            'slug' => 'pizza-americana',
            'estado' => Producto::ESTADO_ACTIVO,
            'disponible' => true,
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $business = NegocioAfiliado::factory()->create();
        $category = Categoria::factory()->create();
        $product = Producto::factory()->create(['nombre' => 'Cafe helado', 'slug' => 'cafe-helado']);

        $this->actingAs($admin)
            ->put("/admin/products/{$product->id}", [
                'negocio_afiliado_id' => $business->id,
                'categoria_id' => $category->id,
                'nombre' => 'Cafe helado especial',
                'descripcion' => 'Cafe con crema',
                'precio' => '12.00',
                'precio_promocional' => '',
                'imagen' => '',
                'estado' => Producto::ESTADO_INACTIVO,
                'disponible' => '0',
            ])
            ->assertRedirect('/admin/products');

        $this->assertDatabaseHas('productos', [
            'id' => $product->id,
            'nombre' => 'Cafe helado especial',
            'slug' => 'cafe-helado-especial',
            'estado' => Producto::ESTADO_INACTIVO,
            'disponible' => false,
        ]);
    }

    public function test_products_can_be_filtered_by_business_and_category(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $business = NegocioAfiliado::factory()->create();
        $category = Categoria::factory()->create();
        Producto::factory()->create([
            'nombre' => 'Combo familiar',
            'negocio_afiliado_id' => $business->id,
            'categoria_id' => $category->id,
        ]);
        Producto::factory()->create(['nombre' => 'Producto oculto']);

        $this->actingAs($admin)
            ->get("/admin/products?negocio_afiliado_id={$business->id}&categoria_id={$category->id}")
            ->assertOk()
            ->assertSee('Combo familiar')
            ->assertDontSee('Producto oculto');
    }

    public function test_admin_can_soft_delete_product(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $product = Producto::factory()->create();

        $this->actingAs($admin)
            ->delete("/admin/products/{$product->id}")
            ->assertRedirect('/admin/products');

        $this->assertSoftDeleted('productos', ['id' => $product->id]);
    }

    public function test_affiliated_business_cannot_manage_global_products(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/products')
            ->assertForbidden();
    }

    public function test_promotional_price_must_be_less_than_price(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $business = NegocioAfiliado::factory()->create();

        $this->actingAs($admin)
            ->from('/admin/products/create')
            ->post('/admin/products', [
                'negocio_afiliado_id' => $business->id,
                'nombre' => 'Hamburguesa',
                'precio' => '10.00',
                'precio_promocional' => '12.00',
                'estado' => Producto::ESTADO_ACTIVO,
                'disponible' => '1',
            ])
            ->assertRedirect('/admin/products/create')
            ->assertSessionHasErrors('precio_promocional');
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
