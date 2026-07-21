<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_screen_loads_for_guests(): void
    {
        $this->get('/app')
            ->assertOk()
            ->assertSee('TIEMPO')
            ->assertSee('Darme de alta en TIEMPO')
            ->assertDontSee('Dashboard administrativo');
    }

    public function test_login_page_is_accessible(): void
    {
        $this->get('/app/login')
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_registro_page_is_accessible(): void
    {
        $this->get('/app/registro')
            ->assertOk()
            ->assertSee('Crear cuenta');
    }

    public function test_authenticated_customer_sees_home(): void
    {
        $cliente = Cliente::factory()->create(['password' => 'secret123']);

        $this->actingAs($cliente, 'cliente')
            ->get('/app/inicio')
            ->assertOk()
            ->assertSee('Negocios afiliados');
    }

    public function test_guest_is_redirected_to_login_when_accessing_inicio(): void
    {
        $this->get('/app/inicio')
            ->assertRedirect(route('app.login'));
    }

    public function test_authenticated_customer_redirected_to_inicio_from_login(): void
    {
        $cliente = Cliente::factory()->create(['password' => 'secret123']);

        $this->actingAs($cliente, 'cliente')
            ->get('/app/login')
            ->assertRedirect(route('app.inicio'));
    }

    public function test_customer_can_register(): void
    {
        NegocioAfiliado::factory()->create(); // para evitar errores de relaciones

        $this->post('/app/registro', [
            'nombres'               => 'Juan Pérez',
            'telefono'              => '987654321',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terminos'              => '1',
        ])->assertRedirect(route('app.inicio'));

        $this->assertDatabaseHas('clientes', [
            'telefono' => '987654321',
            'nombres'  => 'Juan Pérez',
        ]);

        $cliente = Cliente::where('telefono', '987654321')->first();
        $this->assertNotNull($cliente->codigo_cliente);
        $this->assertStringStartsWith('CLI-', $cliente->codigo_cliente);
    }

    public function test_customer_can_login_with_phone_and_password(): void
    {
        $cliente = Cliente::factory()->create([
            'telefono' => '912345678',
            'password' => 'secret123',
        ]);

        $this->post('/app/login', [
            'telefono' => '912345678',
            'password' => 'secret123',
        ])->assertRedirect(route('app.inicio'));

        $this->assertAuthenticatedAs($cliente, 'cliente');
    }

    public function test_customer_login_fails_with_wrong_password(): void
    {
        Cliente::factory()->create([
            'telefono' => '912345678',
            'password' => 'correct-password',
        ]);

        $this->post('/app/login', [
            'telefono' => '912345678',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors(['telefono']);
    }

    public function test_customer_can_logout(): void
    {
        $cliente = Cliente::factory()->create(['password' => 'secret123']);

        $this->actingAs($cliente, 'cliente')
            ->post('/app/logout')
            ->assertRedirect(route('app.home'));

        $this->assertGuest('cliente');
    }

    public function test_perfil_requires_auth(): void
    {
        $this->get('/app/perfil')
            ->assertRedirect(route('app.login'));
    }

    public function test_perfil_loads_for_authenticated_cliente(): void
    {
        $cliente = Cliente::factory()->create(['password' => 'secret123']);

        $this->actingAs($cliente, 'cliente')
            ->get('/app/perfil')
            ->assertOk()
            ->assertSee($cliente->nombres);
    }

    public function test_mobile_app_manifest_is_available(): void
    {
        $this->get('/app/manifest.webmanifest')
            ->assertOk()
            ->assertHeader('content-type', 'application/manifest+json')
            ->assertJsonPath('name', 'TIEMPO Delivery')
            ->assertJsonPath('start_url', '/app')
            ->assertJsonPath('scope', '/app/');
    }

    public function test_mobile_app_service_worker_does_not_cache_sensitive_data(): void
    {
        $this->get('/app/service-worker.js')
            ->assertOk()
            ->assertSee('STATIC_ASSETS')
            ->assertSee('/css/app-mobile.css')
            ->assertSee('/js/app-mobile.js')
            ->assertSee('No cachear datos de clientes, pedidos, pagos ni sesiones');
        $this->get('/app/service-worker.js')
            ->assertSee("self.addEventListener('push'", false)
            ->assertSee("self.addEventListener('notificationclick'", false);
    }
}
