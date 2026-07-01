@extends('layouts.admin')

@section('title', 'Repartidores')
@section('eyebrow', 'Operacion de delivery')
@section('page-title', 'Repartidores')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Equipo de reparto</h2>
                <p>TIEMPO gestiona la disponibilidad y asignacion de repartidores. Los negocios afiliados no acceden a este modulo.</p>
            </div>
            <a class="admin-button admin-button-dark" href="{{ route('admin.couriers.create') }}">Nuevo repartidor</a>
        </div>

        @if (session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif

        <form class="admin-filter-bar admin-filter-bar-compact" method="GET" action="{{ route('admin.couriers.index') }}">
            <label class="admin-field">
                <span>Buscar</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, telefono, documento o placa">
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
                <a class="admin-button" href="{{ route('admin.couriers.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Repartidor</th>
                        <th>Telefono</th>
                        <th>Vehiculo</th>
                        <th>Pedidos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($couriers as $courier)
                        <tr>
                            <td>
                                <strong>{{ $courier->nombreCompleto() }}</strong>
                                <small>{{ $courier->documento ?: 'Sin documento' }}</small>
                            </td>
                            <td>{{ $courier->telefono }}</td>
                            <td>{{ $courier->vehiculo_tipo ?: 'Sin vehiculo' }} {{ $courier->vehiculo_placa ? '('.$courier->vehiculo_placa.')' : '' }}</td>
                            <td>{{ $courier->pedidos_count }}</td>
                            <td>
                                <span class="admin-badge {{ $courier->estado === 'disponible' ? 'admin-badge-green' : ($courier->estado === 'ocupado' ? 'admin-badge-yellow' : 'admin-badge-red') }}">
                                    {{ $estadoOptions[$courier->estado] ?? ucfirst($courier->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a class="admin-link" href="{{ route('admin.couriers.edit', $courier) }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.couriers.destroy', $courier) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-link admin-link-danger" type="submit">Desactivar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay repartidores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $couriers->links() }}
        </div>
    </section>
@endsection
