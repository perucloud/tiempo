<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewPaymentRequest;
use App\Models\Pago;
use App\Models\Pedido;
use App\Services\NotificationService;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Pago::query()
            ->with(['pedido.cliente', 'pedido.negocioAfiliado'])
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('metodo'), fn ($query) => $query->where('metodo', $request->string('metodo')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.payments.index', [
            'adminModules' => AdminNavigation::for('pagos'),
            'payments' => $payments,
            'estadoOptions' => Pago::estadoOptions(),
            'metodoOptions' => Pago::metodoOptions(),
            'filters' => $request->only(['estado', 'metodo']),
        ]);
    }

    public function edit(Pago $payment): View
    {
        return view('admin.payments.edit', [
            'adminModules' => AdminNavigation::for('pagos'),
            'payment' => $payment->load(['pedido.cliente', 'pedido.negocioAfiliado', 'verificador']),
            'estadoOptions' => Pago::estadoOptions(),
            'metodoOptions' => Pago::metodoOptions(),
        ]);
    }

    public function update(ReviewPaymentRequest $request, Pago $payment, NotificationService $notifications): RedirectResponse
    {
        $data = $request->validated();
        $pedido = $payment->pedido;
        $previous = $pedido->estado;
        $newOrderState = $data['estado'] === Pago::ESTADO_APROBADO
            ? Pedido::ESTADO_CONFIRMADO
            : Pedido::ESTADO_PENDIENTE;
        $newPaymentState = $data['estado'] === Pago::ESTADO_APROBADO
            ? Pedido::PAGO_APROBADO
            : Pedido::PAGO_RECHAZADO;

        $payment->update([
            'estado' => $data['estado'],
            'observacion' => $data['observacion'] ?? null,
            'verificado_por' => $request->user()->id,
            'verificado_at' => now(),
        ]);

        $pedido->update([
            'estado' => $newOrderState,
            'estado_pago' => $newPaymentState,
        ]);
        $pedido->estados()->create([
            'user_id' => $request->user()->id,
            'estado_anterior' => $previous,
            'estado_nuevo' => $newOrderState,
            'comentario' => $data['observacion'] ?? 'Revision de pago registrada.',
        ]);
        $notifications->paymentReviewed($payment->refresh());

        return redirect()
            ->route('admin.payments.edit', $payment)
            ->with('status', 'Pago revisado correctamente.');
    }
}
