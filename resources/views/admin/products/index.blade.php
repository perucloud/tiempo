@extends('layouts.admin')

@section('title', 'Productos')
@section('eyebrow', 'Carta digital')
@section('page-title', 'Productos')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Productos por negocio afiliado</h2>
                <p>TIEMPO usa estos productos para vender a clientes desde la app movil.</p>
            </div>
            <a class="admin-button admin-button-dark" href="{{ route('admin.products.create') }}">Nuevo producto</a>
        </div>

        @if (session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif

        <form class="admin-filter-bar" method="GET" action="{{ route('admin.products.index') }}">
            <label class="admin-field">
                <span>Buscar</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre de producto">
            </label>

            <label class="admin-field">
                <span>Negocio</span>
                <select name="negocio_afiliado_id">
                    <option value="">Todos</option>
                    @foreach ($businesses as $business)
                        <option value="{{ $business->id }}" @selected((string) ($filters['negocio_afiliado_id'] ?? '') === (string) $business->id)>{{ $business->nombre_comercial }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field">
                <span>Categoria</span>
                <select name="categoria_id">
                    <option value="">Todas</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['categoria_id'] ?? '') === (string) $category->id)>{{ $category->nombre }}</option>
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
                <a class="admin-button" href="{{ route('admin.products.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Negocio</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Disponible</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->nombre }}</strong><br>
                                <small>{{ $product->slug }}</small>
                            </td>
                            <td>{{ $product->negocioAfiliado?->nombre_comercial }}</td>
                            <td>{{ $product->categoria?->nombre ?: 'Sin categoria' }}</td>
                            <td>{{ $product->precioVenta() }}</td>
                            <td>
                                <span class="admin-badge {{ $product->estado === 'activo' ? 'admin-badge-green' : 'admin-badge-red' }}">
                                    {{ $estadoOptions[$product->estado] ?? ucfirst($product->estado) }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge {{ $product->disponible ? 'admin-badge-green' : 'admin-badge-yellow' }}">
                                    {{ $product->disponible ? 'Si' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a class="admin-link" href="{{ route('admin.products.edit', $product) }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-link admin-link-danger" type="submit">Desactivar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $products->links() }}
        </div>
    </section>
@endsection
