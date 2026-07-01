@extends('layouts.admin')

@section('title', 'Revisar pago')
@section('eyebrow', 'Verificacion')
@section('page-title', 'Pago pedido '.$payment->pedido?->codigo)

@section('content')
    <section class="admin-grid admin-grid-3">
        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Resumen</h2>
                    <p>{{ $payment->pedido?->cliente?->nombreCompleto() }} | {{ $payment->pedido?->cliente?->telefono }}</p>
                </div>
            </div>
            <div class="admin-module-list">
                <div class="admin-module-item"><strong>Metodo</strong><span>{{ $metodoOptions[$payment->metodo] ?? $payment->metodo }}</span></div>
                <div class="admin-module-item"><strong>Monto</strong><span>S/ {{ number_format((float) $payment->monto, 2) }}</span></div>
                <div class="admin-module-item"><strong>Operacion</strong><span>{{ $payment->codigo_operacion ?: 'Sin codigo' }}</span></div>
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Voucher</h2>
                    <p>Validar contra Yape/Plin antes de aprobar.</p>
                </div>
            </div>
            @if ($payment->voucher_path)
                <a class="admin-link" href="{{ $payment->voucher_path }}" target="_blank" rel="noreferrer">Abrir voucher</a>
            @else
                <p>Sin voucher registrado.</p>
            @endif
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Revision</h2>
                    <p>El resultado impacta el estado del pedido.</p>
                </div>
            </div>

            @if (session('status'))
                <div class="admin-alert">{{ session('status') }}</div>
            @endif

            <form class="admin-form" method="POST" action="{{ route('admin.payments.update', $payment) }}">
                @csrf
                @method('PUT')
                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </label>
                <label class="admin-field">
                    <span>Observacion</span>
                    <textarea name="observacion" rows="3">{{ old('observacion', $payment->observacion) }}</textarea>
                </label>
                <div class="admin-form-actions">
                    <button class="admin-button admin-button-dark" type="submit">Guardar revision</button>
                </div>
            </form>
        </article>
    </section>
@endsection
