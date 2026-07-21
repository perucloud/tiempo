<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'codigo'           => ['required', 'exists:pedidos,codigo'],
            'metodo'           => ['required', Rule::in(Pago::METODOS)],
            'codigo_operacion' => ['nullable', 'string', 'max:120'],
            'voucher'          => ['nullable', 'file', 'image', 'max:5120'], // 5 MB
        ]);

        $pedido = Pedido::query()->where('codigo', $data['codigo'])->firstOrFail();
        abort_unless(in_array($pedido->id, array_map('intval', session('app_order_ids', [])), true), 404);

        if ($pedido->estado !== Pedido::ESTADO_PENDIENTE || $pedido->estado_pago !== Pedido::PAGO_PENDIENTE) {
            return redirect()->route('app.orders.show', $pedido->codigo)
                ->with('pay_error', 'Este pedido ya tiene un pago registrado o no admite pagos.');
        }

        /* Guardar imagen del voucher si se subió */
        $voucherPath = null;
        if ($request->hasFile('voucher') && $request->file('voucher')->isValid()) {
            $voucherPath = $request->file('voucher')->store('vouchers', 'public');
        }

        DB::transaction(function () use ($pedido, $data, $voucherPath): void {
            $locked = Pedido::query()->lockForUpdate()->findOrFail($pedido->id);

            abort_unless($locked->estado === Pedido::ESTADO_PENDIENTE
                && $locked->estado_pago === Pedido::PAGO_PENDIENTE, 409);

            $pago = $locked->pagos()->create([
                'metodo'           => $data['metodo'],
                'monto'            => $locked->total,
                'estado'           => Pago::ESTADO_PENDIENTE,
                'voucher_path'     => $voucherPath,
                'codigo_operacion' => $data['codigo_operacion'] ?? null,
            ]);

            $estadoAnterior = $locked->estado;
            $locked->update([
                'estado'      => Pedido::ESTADO_PAGO_EN_REVISION,
                'estado_pago' => Pedido::PAGO_EN_REVISION,
            ]);

            $locked->estados()->create([
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => Pedido::ESTADO_PAGO_EN_REVISION,
                'comentario'      => "Pago {$pago->id} registrado desde la app.",
            ]);
        });

        return redirect()
            ->route('app.orders.show', $pedido->codigo)
            ->with('pay_status', 'Pago enviado. Te avisaremos cuando sea confirmado.');
    }
}
