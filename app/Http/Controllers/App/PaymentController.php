<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'codigo' => ['required', 'exists:pedidos,codigo'],
            'metodo' => ['required', Rule::in(Pago::METODOS)],
            'codigo_operacion' => ['nullable', 'string', 'max:120'],
            'voucher_path' => ['nullable', 'url', 'max:255'],
        ]);

        $pedido = Pedido::query()->where('codigo', $data['codigo'])->firstOrFail();

        $pago = $pedido->pagos()->create([
            'metodo' => $data['metodo'],
            'monto' => $pedido->total,
            'estado' => Pago::ESTADO_PENDIENTE,
            'voucher_path' => $data['voucher_path'] ?? null,
            'codigo_operacion' => $data['codigo_operacion'] ?? null,
        ]);

        $pedido->update([
            'estado' => Pedido::ESTADO_PAGO_EN_REVISION,
            'estado_pago' => Pedido::PAGO_EN_REVISION,
        ]);
        $pedido->estados()->create([
            'estado_anterior' => Pedido::ESTADO_PENDIENTE,
            'estado_nuevo' => Pedido::ESTADO_PAGO_EN_REVISION,
            'comentario' => "Pago {$pago->id} registrado desde la app.",
        ]);

        return redirect()
            ->to(route('app.home').'#pedidos')
            ->with('order_status', "Pago registrado para revision del pedido {$pedido->codigo}.");
    }
}
