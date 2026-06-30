<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_categories_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        Categoria::factory()->create(['nombre' => 'Bebidas frias']);

        $this->actingAs($operator)
            ->get('/admin/categories')
            ->assertOk()
            ->assertSee('Gestion de categorias')
            ->assertSee('Bebidas frias')
            ->assertSee('Nueva categoria');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->post('/admin/categories', [
                'nombre' => 'Comida criolla',
                'tipo' => Categoria::TIPO_PRODUCTO,
                'estado' => Categoria::ESTADO_ACTIVO,
                'orden' => 5,
            ])
            ->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Comida criolla',
            'slug' => 'comida-criolla',
            'tipo' => Categoria::TIPO_PRODUCTO,
            'estado' => Categoria::ESTADO_ACTIVO,
            'orden' => 5,
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $category = Categoria::factory()->create(['nombre' => 'Postres', 'slug' => 'postres']);

        $this->actingAs($admin)
            ->put("/admin/categories/{$category->id}", [
                'nombre' => 'Postres dulces',
                'tipo' => Categoria::TIPO_PROMOCION,
                'estado' => Categoria::ESTADO_INACTIVO,
                'orden' => 9,
            ])
            ->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categorias', [
            'id' => $category->id,
            'nombre' => 'Postres dulces',
            'slug' => 'postres-dulces',
            'tipo' => Categoria::TIPO_PROMOCION,
            'estado' => Categoria::ESTADO_INACTIVO,
            'orden' => 9,
        ]);
    }

    public function test_admin_cannot_create_duplicate_active_category_name(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        Categoria::factory()->create(['nombre' => 'Bebidas']);

        $this->actingAs($admin)
            ->from('/admin/categories/create')
            ->post('/admin/categories', [
                'nombre' => 'Bebidas',
                'tipo' => Categoria::TIPO_PRODUCTO,
                'estado' => Categoria::ESTADO_ACTIVO,
                'orden' => 2,
            ])
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHasErrors('nombre');
    }

    public function test_categories_can_be_filtered_by_search_and_status(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        Categoria::factory()->create(['nombre' => 'Bebidas calientes', 'estado' => Categoria::ESTADO_ACTIVO]);
        Categoria::factory()->create(['nombre' => 'Farmacia', 'estado' => Categoria::ESTADO_INACTIVO]);

        $this->actingAs($admin)
            ->get('/admin/categories?search=Bebidas&estado=activo')
            ->assertOk()
            ->assertSee('Bebidas calientes')
            ->assertDontSee('Farmacia');
    }

    public function test_admin_can_soft_delete_category(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $category = Categoria::factory()->create();

        $this->actingAs($admin)
            ->delete("/admin/categories/{$category->id}")
            ->assertRedirect('/admin/categories');

        $this->assertSoftDeleted('categorias', ['id' => $category->id]);
    }

    public function test_affiliated_business_cannot_manage_global_categories(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/categories')
            ->assertForbidden();
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
