<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_loads_publicly(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('TIEMPO Delivery')
            ->assertSee('Empresa de delivery, no restaurante')
            ->assertSee('Afiliar mi negocio')
            ->assertSee('Pedir desde la app')
            ->assertSee('/admin/login');
    }
}
