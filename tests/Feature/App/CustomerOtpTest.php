<?php

namespace Tests\Feature\App;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_recover_order_access_with_otp(): void
    {
        [$cliente, $pedido] = $this->makeCustomerWithOrder();

        $issue = $this->postJson(route('app.perfil.codigo'), ['telefono' => $cliente->telefono])
            ->assertOk()
            ->assertJsonStructure(['message', 'debug_code']);

        $code = $issue->json('debug_code');

        $this->postJson(route('app.perfil.verificar'), [
            'telefono' => $cliente->telefono,
            'codigo' => $code,
        ])->assertOk()->assertJsonPath('telefono', $cliente->telefono);

        $this->get(route('app.orders.show', $pedido->codigo))->assertOk();
        $this->postJson(route('app.perfil.buscar'), ['telefono' => $cliente->telefono])
            ->assertOk()->assertJsonFragment(['codigo' => $pedido->codigo]);
    }

    public function test_invalid_otp_does_not_grant_access(): void
    {
        [$cliente, $pedido] = $this->makeCustomerWithOrder();
        $this->postJson(route('app.perfil.codigo'), ['telefono' => $cliente->telefono])->assertOk();

        $this->postJson(route('app.perfil.verificar'), [
            'telefono' => $cliente->telefono,
            'codigo' => '000000',
        ])->assertUnprocessable();

        $this->get(route('app.orders.show', $pedido->codigo))->assertRedirect(route('app.home'));
    }

    public function test_otp_is_single_use(): void
    {
        [$cliente] = $this->makeCustomerWithOrder();
        $code = $this->postJson(route('app.perfil.codigo'), ['telefono' => $cliente->telefono])->json('debug_code');
        $payload = ['telefono' => $cliente->telefono, 'codigo' => $code];

        $this->postJson(route('app.perfil.verificar'), $payload)->assertOk();
        $this->postJson(route('app.perfil.verificar'), $payload)->assertUnprocessable();
    }

    public function test_code_request_does_not_reveal_whether_customer_exists(): void
    {
        $known = Cliente::factory()->create(['telefono' => '999111222']);

        $knownMessage = $this->postJson(route('app.perfil.codigo'), ['telefono' => $known->telefono])->json('message');
        $unknownMessage = $this->postJson(route('app.perfil.codigo'), ['telefono' => '988777666'])->json('message');

        $this->assertSame($knownMessage, $unknownMessage);
    }

    private function makeCustomerWithOrder(): array
    {
        $cliente = Cliente::factory()->create(['telefono' => '999111222']);
        $pedido = Pedido::factory()->create([
            'cliente_id' => $cliente->id,
            'negocio_afiliado_id' => NegocioAfiliado::factory()->create()->id,
        ]);

        return [$cliente, $pedido];
    }
}
