@extends('layouts.admin')

@section('title', 'Negocios afiliados')
@section('eyebrow', 'Red comercial')
@section('page-title', 'Negocios afiliados')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Negocios afiliados</h2>
                <p>TIEMPO administra la operacion; los negocios proveen informacion comercial y carta.</p>
            </div>
            <a class="admin-button admin-button-dark" href="{{ route('admin.businesses.create') }}">Nuevo negocio</a>
        </div>

        @if (session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif

        <form class="admin-filter-bar" method="GET" action="{{ route('admin.businesses.index') }}">
            <label class="admin-field">
                <span>Buscar</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, telefono o RUC">
            </label>

            <label class="admin-field">
                <span>Tipo</span>
                <select name="tipo_negocio">
                    <option value="">Todos</option>
                    @foreach ($tipoOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['tipo_negocio'] ?? '') === $value)>{{ $label }}</option>
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
                <a class="admin-button" href="{{ route('admin.businesses.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Negocio</th>
                        <th>Tipo</th>
                        <th>Telefono</th>
                        <th>Estado</th>
                        <th>Atencion</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($businesses as $business)
                        <tr>
                            <td>
                                <strong>{{ $business->nombre_comercial }}</strong><br>
                                <small>{{ $business->direccion ?: 'Sin direccion' }}</small>
                            </td>
                            <td>{{ $tipoOptions[$business->tipo_negocio] ?? ucfirst($business->tipo_negocio) }}</td>
                            <td>{{ $business->telefono ?: 'Sin telefono' }}</td>
                            <td>
                                <span class="admin-badge {{ $business->estado === 'activo' ? 'admin-badge-green' : 'admin-badge-red' }}">
                                    {{ $estadoOptions[$business->estado] ?? ucfirst($business->estado) }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge {{ $business->abierto ? 'admin-badge-green' : 'admin-badge-yellow' }}">
                                    {{ $business->abierto ? 'Abierto' : 'Cerrado' }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a class="admin-link" href="{{ route('admin.businesses.edit', $business) }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.businesses.destroy', $business) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-link admin-link-danger" type="submit">Desactivar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay negocios afiliados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $businesses->links() }}
        </div>
    </section>
@endsection
