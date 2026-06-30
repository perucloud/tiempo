<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    public function test_api_health_endpoint_returns_uniform_json(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['service', 'status', 'version'],
                'message',
                'errors',
                'meta' => ['consumers'],
            ])
            ->assertJsonPath('data.service', 'TIEMPO Delivery API')
            ->assertJsonPath('data.status', 'ok')
            ->assertJsonPath('data.version', 'v1')
            ->assertJsonPath('errors', null);
    }

    public function test_api_missing_route_returns_json(): void
    {
        $this->getJson('/api/v1/no-existe')
            ->assertNotFound()
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['data', 'message', 'errors', 'meta'])
            ->assertJsonPath('data', null)
            ->assertJsonPath('message', 'Endpoint no encontrado.');
    }
}
