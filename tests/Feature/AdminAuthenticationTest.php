<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_area_redirects_guests_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_superadmin_can_login_to_admin_dashboard(): void
    {
        User::query()->create([
            'name' => 'SuperAdmin TIEMPO',
            'email' => 'admin@tiempo.test',
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_SUPERADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@tiempo.test',
            'password' => 'secret-password',
        ])->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard administrativo')
            ->assertSee('Pedidos')
            ->assertSee('Pagos')
            ->assertSee('Repartidores')
            ->assertSee('Negocios afiliados')
            ->assertSee('Operacion rapida movil');
    }

    public function test_client_role_cannot_access_admin_dashboard(): void
    {
        $client = User::query()->create([
            'name' => 'Cliente TIEMPO',
            'email' => 'cliente@tiempo.test',
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_CLIENTE,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($client)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::query()->create([
            'name' => 'Operador TIEMPO',
            'email' => 'operador@tiempo.test',
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_OPERADOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }
}
