@extends('layouts.admin')

@section('title', 'Pedidos')
@section('eyebrow', 'Operacion')
@section('page-title', 'Pedidos')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Pedidos recibidos</h2>
                <p>TIEMPO verifica, opera y actualiza los estados del pedido.</p>
            </div>
        </div>

        <form class="admin-filter-bar admin-filter-bar-compact" method="GET" action="{{ route('admin.orders.index') }}">
            <label class="admin-field">
                <span>Buscar</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Codigo, cliente o telefono">
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
                <a class="admin-button" href="{{ route('admin.orders.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Cliente</th>
                        <th>Negocio</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->codigo }}</td>
                            <td>{{ $order->cliente?->nombreCompleto() }}<br><small>{{ $order->cliente?->telefono }}</small></td>
                            <td>{{ $order->negocioAfiliado?->nombre_comercial }}</td>
                            <td><span class="admin-badge admin-badge-yellow">{{ $order->estadoLabel() }}</span></td>
                            <td>S/ {{ number_format((float) $order->total, 2) }}</td>
                            <td><a class="admin-link" href="{{ route('admin.orders.edit', $order) }}">Gestionar</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay pedidos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $orders->links() }}
        </div>
    </section>
@endsection
