@extends('layouts.admin')

@section('title', 'Clientes')
@section('eyebrow', 'Base de clientes')
@section('page-title', 'Clientes')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Clientes registrados</h2>
                <p>TIEMPO centraliza los datos de clientes. Los negocios afiliados no acceden a esta informacion.</p>
            </div>
            <a class="admin-button admin-button-dark" href="{{ route('admin.clients.create') }}">Nuevo cliente</a>
        </div>

        @if (session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif

        <form class="admin-filter-bar admin-filter-bar-compact" method="GET" action="{{ route('admin.clients.index') }}">
            <label class="admin-field">
                <span>Buscar</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, telefono, documento o email">
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
                <a class="admin-button" href="{{ route('admin.clients.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Telefono</th>
                        <th>Email</th>
                        <th>Documento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td>{{ $client->nombreCompleto() }}</td>
                            <td>{{ $client->telefono }}</td>
                            <td>{{ $client->email ?: 'Sin email' }}</td>
                            <td>{{ $client->documento ?: 'Sin documento' }}</td>
                            <td>
                                <span class="admin-badge {{ $client->estado === 'activo' ? 'admin-badge-green' : 'admin-badge-red' }}">
                                    {{ $estadoOptions[$client->estado] ?? ucfirst($client->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a class="admin-link" href="{{ route('admin.clients.edit', $client) }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.clients.destroy', $client) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-link admin-link-danger" type="submit">Desactivar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay clientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $clients->links() }}
        </div>
    </section>
@endsection
