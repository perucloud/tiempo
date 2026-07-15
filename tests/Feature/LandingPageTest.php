<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_loads_publicly(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Tiempo Delivery')
            ->assertSee('Quiero pedir')
            ->assertSee('Registrar mi restaurante')
            ->assertSee('/admin/login');
    }
}
