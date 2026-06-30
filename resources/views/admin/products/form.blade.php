@extends('layouts.admin')

@section('title', $product->exists ? 'Editar producto' : 'Nuevo producto')
@section('eyebrow', 'Carta digital')
@section('page-title', $product->exists ? 'Editar producto' : 'Nuevo producto')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $product->exists ? 'Actualizar producto' : 'Crear producto' }}</h2>
                <p>Los productos pertenecen a negocios afiliados y son vendidos por TIEMPO a clientes.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.products.index') }}">Volver</a>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Negocio afiliado</span>
                    <select name="negocio_afiliado_id" required>
                        <option value="">Seleccionar</option>
                        @foreach ($businesses as $business)
                            <option value="{{ $business->id }}" @selected((string) old('negocio_afiliado_id', $product->negocio_afiliado_id) === (string) $business->id)>{{ $business->nombre_comercial }}</option>
                        @endforeach
                    </select>
                    @error('negocio_afiliado_id') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Categoria</span>
                    <select name="categoria_id">
                        <option value="">Sin categoria</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('categoria_id', $product->categoria_id) === (string) $category->id)>{{ $category->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Nombre</span>
                    <input type="text" name="nombre" value="{{ old('nombre', $product->nombre) }}" required>
                    @error('nombre') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Imagen URL</span>
                    <input type="url" name="imagen" value="{{ old('imagen', $product->imagen) }}">
                    @error('imagen') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Precio</span>
                    <input type="number" name="precio" min="0.1" step="0.01" value="{{ old('precio', $product->precio) }}" required>
                    @error('precio') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Precio promocional</span>
                    <input type="number" name="precio_promocional" min="0.1" step="0.01" value="{{ old('precio_promocional', $product->precio_promocional) }}">
                    @error('precio_promocional') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        @foreach ($estadoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $product->estado) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Disponible</span>
                    <select name="disponible" required>
                        <option value="1" @selected((string) old('disponible', (int) $product->disponible) === '1')>Si</option>
                        <option value="0" @selected((string) old('disponible', (int) $product->disponible) === '0')>No</option>
                    </select>
                    @error('disponible') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field admin-field-wide">
                    <span>Descripcion</span>
                    <textarea name="descripcion" rows="4">{{ old('descripcion', $product->descripcion) }}</textarea>
                    @error('descripcion') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">
                    {{ $product->exists ? 'Guardar cambios' : 'Crear producto' }}
                </button>
            </div>
        </form>
    </section>
@endsection
