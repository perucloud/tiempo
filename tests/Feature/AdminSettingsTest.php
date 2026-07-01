<?php

namespace Tests\Feature;

use App\Models\ConfiguracionAuditoria;
use App\Models\SistemaConfiguracion;
use App\Models\User;
use App\Models\ZonaDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_index_and_defaults_are_created(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee('Configuracion del sistema')
            ->assertSee('TIEMPO Delivery');

        $this->assertDatabaseHas('sistema_configuraciones', [
            'clave' => 'nombre_sistema',
            'valor' => 'TIEMPO Delivery',
        ]);
    }

    public function test_admin_can_update_general_settings_and_audit_changes(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $this->actingAs($admin)->get('/admin/settings');

        $this->actingAs($admin)
            ->put('/admin/settings', [
                'settings' => [
                    'nombre_sistema' => 'TIEMPO Satipo',
                    'telefono_soporte' => '999888777',
                    'whatsapp_pedidos' => '988777666',
                    'email_contacto' => 'contacto@tiempo.test',
                    'direccion_base' => 'Av. Principal 123',
                    'horario_atencion' => '08:00 a 22:00',
                    'tarifa_base_delivery' => '5.00',
                ],
            ])
            ->assertRedirect('/admin/settings');

        $this->assertDatabaseHas('sistema_configuraciones', [
            'clave' => 'nombre_sistema',
            'valor' => 'TIEMPO Satipo',
        ]);
        $this->assertDatabaseHas('configuracion_auditorias', [
            'user_id' => $admin->id,
            'entidad' => 'sistema_configuraciones',
            'accion' => 'actualizar',
        ]);
    }

    public function test_admin_can_create_update_and_delete_delivery_zone_with_audit(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->post('/admin/delivery-zones', [
                'nombre' => 'Centro',
                'descripcion_cobertura' => 'Zona urbana central',
                'costo_delivery' => '4.50',
                'pedido_minimo' => '20.00',
                'activo' => '1',
            ])
            ->assertRedirect('/admin/settings');

        $zone = ZonaDelivery::query()->firstOrFail();
        $this->assertDatabaseHas('zonas_delivery', [
            'id' => $zone->id,
            'nombre' => 'Centro',
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->put("/admin/delivery-zones/{$zone->id}", [
                'nombre' => 'Centro ampliado',
                'descripcion_cobertura' => 'Zona urbana y periferia',
                'costo_delivery' => '6.00',
                'pedido_minimo' => '25.00',
                'activo' => '1',
            ])
            ->assertRedirect('/admin/settings');

        $this->assertDatabaseHas('zonas_delivery', [
            'id' => $zone->id,
            'nombre' => 'Centro ampliado',
            'costo_delivery' => '6.00',
        ]);

        $this->actingAs($admin)
            ->delete("/admin/delivery-zones/{$zone->id}")
            ->assertRedirect('/admin/settings');

        $this->assertSoftDeleted('zonas_delivery', ['id' => $zone->id]);
        $this->assertGreaterThanOrEqual(3, ConfiguracionAuditoria::query()->where('entidad', 'zonas_delivery')->count());
    }

    public function test_operator_and_affiliated_business_cannot_manage_settings(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($operator)
            ->get('/admin/settings')
            ->assertForbidden();

        $this->actingAs($affiliate)
            ->get('/admin/settings')
            ->assertForbidden();
    }

    public function test_settings_validation_rejects_invalid_email(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        SistemaConfiguracion::query()->create([
            'clave' => 'nombre_sistema',
            'grupo' => 'general',
            'etiqueta' => 'Nombre del sistema',
            'valor' => 'TIEMPO Delivery',
        ]);

        $this->actingAs($admin)
            ->from('/admin/settings')
            ->put('/admin/settings', [
                'settings' => [
                    'nombre_sistema' => 'TIEMPO',
                    'email_contacto' => 'correo-invalido',
                    'tarifa_base_delivery' => '5.00',
                ],
            ])
            ->assertRedirect('/admin/settings')
            ->assertSessionHasErrors('settings.email_contacto');
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
