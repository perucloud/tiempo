<?php

namespace Tests\Feature;

use App\Models\NegocioAfiliado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminBusinessesTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_businesses_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        NegocioAfiliado::factory()->create(['nombre_comercial' => 'Polleria Central']);

        $this->actingAs($operator)
            ->get('/admin/businesses')
            ->assertOk()
            ->assertSee('Negocios afiliados')
            ->assertSee('Polleria Central')
            ->assertSee('Nuevo negocio');
    }

    public function test_admin_can_create_business(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->post('/admin/businesses', [
                'nombre_comercial' => 'Cafe Tiempo',
                'tipo_negocio' => NegocioAfiliado::TIPO_CAFETERIA,
                'ruc' => '20123456789',
                'telefono' => '999888777',
                'email' => 'cafe@tiempo.test',
                'direccion' => 'Av. Central 123',
                'descripcion' => 'Cafe afiliado',
                'estado' => NegocioAfiliado::ESTADO_ACTIVO,
                'abierto' => '1',
                'horarios_texto' => 'Lun-Sab 08:00-20:00',
            ])
            ->assertRedirect('/admin/businesses');

        $this->assertDatabaseHas('negocios_afiliados', [
            'nombre_comercial' => 'Cafe Tiempo',
            'slug' => 'cafe-tiempo',
            'tipo_negocio' => NegocioAfiliado::TIPO_CAFETERIA,
            'estado' => NegocioAfiliado::ESTADO_ACTIVO,
            'abierto' => true,
        ]);
    }

    public function test_admin_can_update_business(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $business = NegocioAfiliado::factory()->create([
            'nombre_comercial' => 'Bodega Norte',
            'slug' => 'bodega-norte',
        ]);

        $this->actingAs($admin)
            ->put("/admin/businesses/{$business->id}", [
                'nombre_comercial' => 'Bodega Norte Express',
                'tipo_negocio' => NegocioAfiliado::TIPO_BODEGA,
                'ruc' => '20999999999',
                'telefono' => '900111222',
                'email' => 'bodega@tiempo.test',
                'direccion' => 'Jr. Norte 456',
                'descripcion' => 'Bodega actualizada',
                'estado' => NegocioAfiliado::ESTADO_INACTIVO,
                'abierto' => '0',
                'horarios_texto' => 'Lun-Vie 09:00-19:00',
            ])
            ->assertRedirect('/admin/businesses');

        $this->assertDatabaseHas('negocios_afiliados', [
            'id' => $business->id,
            'nombre_comercial' => 'Bodega Norte Express',
            'slug' => 'bodega-norte-express',
            'estado' => NegocioAfiliado::ESTADO_INACTIVO,
            'abierto' => false,
        ]);
    }

    public function test_businesses_can_be_filtered_by_search_and_type(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        NegocioAfiliado::factory()->create([
            'nombre_comercial' => 'Farmacia Vida',
            'tipo_negocio' => NegocioAfiliado::TIPO_FARMACIA,
        ]);
        NegocioAfiliado::factory()->create([
            'nombre_comercial' => 'Pizza Centro',
            'tipo_negocio' => NegocioAfiliado::TIPO_PIZZERIA,
        ]);

        $this->actingAs($admin)
            ->get('/admin/businesses?search=Farmacia&tipo_negocio=farmacia')
            ->assertOk()
            ->assertSee('Farmacia Vida')
            ->assertDontSee('Pizza Centro');
    }

    public function test_admin_can_soft_delete_business(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $business = NegocioAfiliado::factory()->create();

        $this->actingAs($admin)
            ->delete("/admin/businesses/{$business->id}")
            ->assertRedirect('/admin/businesses');

        $this->assertSoftDeleted('negocios_afiliados', ['id' => $business->id]);
    }

    public function test_affiliated_business_cannot_manage_global_businesses(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/businesses')
            ->assertForbidden();
    }

    public function test_admin_cannot_create_duplicate_active_business_name(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        NegocioAfiliado::factory()->create(['nombre_comercial' => 'Pizza Centro']);

        $this->actingAs($admin)
            ->from('/admin/businesses/create')
            ->post('/admin/businesses', [
                'nombre_comercial' => 'Pizza Centro',
                'tipo_negocio' => NegocioAfiliado::TIPO_PIZZERIA,
                'estado' => NegocioAfiliado::ESTADO_ACTIVO,
                'abierto' => '1',
            ])
            ->assertRedirect('/admin/businesses/create')
            ->assertSessionHasErrors('nombre_comercial');
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
