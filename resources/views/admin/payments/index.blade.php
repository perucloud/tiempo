@extends('layouts.admin')

@section('title', 'Pagos')
@section('eyebrow', 'Verificacion')
@section('page-title', 'Pagos')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Pagos Yape/Plin</h2>
                <p>TIEMPO revisa vouchers, aprueba o rechaza pagos y actualiza el pedido.</p>
            </div>
        </div>

        <form class="admin-filter-bar admin-filter-bar-compact" method="GET" action="{{ route('admin.payments.index') }}">
            <label class="admin-field">
                <span>Metodo</span>
                <select name="metodo">
                    <option value="">Todos</option>
                    @foreach ($metodoOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['metodo'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field">
                <span>Estado</span>
                <select name="estado">
                    <option value="">Todos</option>
                    @foreach ($estadoOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['estado'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="admin-filter-actions">
                <button class="admin-button admin-button-dark" type="submit">Filtrar</button>
                <a class="admin-button" href="{{ route('admin.payments.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Metodo</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->pedido?->codigo }}</td>
                            <td>{{ $payment->pedido?->cliente?->nombreCompleto() }}</td>
                            <td>{{ $metodoOptions[$payment->metodo] ?? ucfirst($payment->metodo) }}</td>
                            <td>S/ {{ number_format((float) $payment->monto, 2) }}</td>
                            <td><span class="admin-badge admin-badge-yellow">{{ $payment->estadoLabel() }}</span></td>
                            <td><a class="admin-link" href="{{ route('admin.payments.edit', $payment) }}">Revisar</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay pagos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $payments->links() }}
        </div>
    </section>
@endsection
