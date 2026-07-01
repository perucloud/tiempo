@extends('layouts.admin')

@section('title', 'Gestionar pedido')
@section('eyebrow', 'Operacion')
@section('page-title', 'Pedido '.$order->codigo)

@section('content')
    <section class="admin-grid admin-grid-3">
        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Resumen</h2>
                    <p>{{ $order->cliente?->nombreCompleto() }} | {{ $order->cliente?->telefono }}</p>
                </div>
            </div>
            <div class="admin-module-list">
                <div class="admin-module-item"><strong>Negocio</strong><span>{{ $order->negocioAfiliado?->nombre_comercial }}</span></div>
                <div class="admin-module-item"><strong>Direccion</strong><span>{{ $order->direccion_entrega }}</span></div>
                <div class="admin-module-item"><strong>Total</strong><span>S/ {{ number_format((float) $order->total, 2) }}</span></div>
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Productos</h2>
                    <p>Detalle congelado del pedido.</p>
                </div>
            </div>
            <div class="admin-module-list">
                @foreach ($order->detalles as $detail)
                    <div class="admin-module-item">
                        <span>
                            <strong>{{ $detail->producto_nombre }}</strong>
                            <small>{{ $detail->cantidad }} x S/ {{ number_format((float) $detail->precio_unitario, 2) }}</small>
                        </span>
                        <span>S/ {{ number_format((float) $detail->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Estado</h2>
                    <p>Actualiza el avance operativo.</p>
                </div>
            </div>

            @if (session('status'))
                <div class="admin-alert">{{ session('status') }}</div>
            @endif

            <form class="admin-form" method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PUT')
                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        @foreach ($estadoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $order->estado) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="admin-field">
                    <span>Comentario</span>
                    <textarea name="comentario" rows="3">{{ old('comentario') }}</textarea>
                </label>
                <div class="admin-form-actions">
                    <button class="admin-button admin-button-dark" type="submit">Actualizar estado</button>
                </div>
            </form>
        </article>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Historial</h2>
                <p>Auditoria de estados del pedido.</p>
            </div>
        </div>
        <div class="admin-module-list">
            @foreach ($order->estados as $state)
                <div class="admin-module-item">
                    <span>
                        <strong>{{ $estadoOptions[$state->estado_nuevo] ?? $state->estado_nuevo }}</strong>
                        <small>{{ $state->comentario ?: 'Sin comentario' }}</small>
                    </span>
                    <span>{{ $state->created_at?->format('d/m/Y H:i') }}</span>
                </div>
            @endforeach
        </div>
    </section>
@endsection
