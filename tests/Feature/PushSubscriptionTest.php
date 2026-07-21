<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private array $subscription = [
        'endpoint' => 'https://push.example.test/subscription/abc',
        'keys' => ['p256dh' => 'public-key', 'auth' => 'auth-token'],
        'contentEncoding' => 'aes128gcm',
    ];

    public function test_verified_customer_can_store_push_subscription(): void
    {
        $customer = Cliente::factory()->create();

        $this->actingAs($customer, 'cliente')
            ->postJson(route('app.push.subscribe'), $this->subscription)
            ->assertOk();

        $this->assertDatabaseHas('push_subscriptions', [
            'cliente_id'    => $customer->id,
            'endpoint_hash' => hash('sha256', $this->subscription['endpoint']),
        ]);
    }

    public function test_unauthenticated_customer_cannot_store_push_subscription(): void
    {
        $this->postJson(route('app.push.subscribe'), $this->subscription)->assertUnauthorized();
        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_authenticated_courier_can_store_own_push_subscription(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_REPARTIDOR, 'status' => User::STATUS_ACTIVE]);
        $courier = Repartidor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->postJson(route('courier.push.subscribe', $courier), $this->subscription)->assertOk();
        $this->assertDatabaseHas('push_subscriptions', ['repartidor_id' => $courier->id]);
    }
}
