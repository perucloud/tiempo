@extends('layouts.admin')

@section('title', 'Categorias')
@section('eyebrow', 'Catalogo global')
@section('page-title', 'Gestion de categorias')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Categorias globales</h2>
                <p>Organiza comidas, bebidas, promociones y futuras categorias usadas por TIEMPO.</p>
            </div>
            <a class="admin-button admin-button-dark" href="{{ route('admin.categories.create') }}">Nueva categoria</a>
        </div>

        @if (session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif

        <form class="admin-filter-bar" method="GET" action="{{ route('admin.categories.index') }}">
            <label class="admin-field">
                <span>Buscar</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre de categoria">
            </label>

            <label class="admin-field">
                <span>Tipo</span>
                <select name="tipo">
                    <option value="">Todos</option>
                    @foreach ($tipoOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['tipo'] ?? '') === $value)>{{ $label }}</option>
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
                <a class="admin-button" href="{{ route('admin.categories.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->orden }}</td>
                            <td>{{ $category->nombre }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $tipoOptions[$category->tipo] ?? ucfirst($category->tipo) }}</td>
                            <td>
                                <span class="admin-badge {{ $category->estado === 'activo' ? 'admin-badge-green' : 'admin-badge-red' }}">
                                    {{ $estadoOptions[$category->estado] ?? ucfirst($category->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a class="admin-link" href="{{ route('admin.categories.edit', $category) }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-link admin-link-danger" type="submit">Desactivar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay categorias registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $categories->links() }}
        </div>
    </section>
@endsection
