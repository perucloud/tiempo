<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_mobile_app_loads_public_base(): void
    {
        $this->get('/app')
            ->assertOk()
            ->assertSee('TIEMPO App')
            ->assertSee('Negocios afiliados')
            ->assertSee('Carrito')
            ->assertSee('Seguimiento')
            ->assertSee('Perfil cliente')
            ->assertDontSee('Dashboard administrativo');
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
            ->assertSee('No cachear datos de clientes, pedidos, pagos ni sesiones');
    }
}
