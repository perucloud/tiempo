@extends('layouts.admin')

@section('title', $category->exists ? 'Editar categoria' : 'Nueva categoria')
@section('eyebrow', 'Catalogo global')
@section('page-title', $category->exists ? 'Editar categoria' : 'Nueva categoria')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $category->exists ? 'Actualizar categoria' : 'Crear categoria' }}</h2>
                <p>Estas categorias son globales y operadas por TIEMPO.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.categories.index') }}">Volver</a>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Nombre</span>
                    <input type="text" name="nombre" value="{{ old('nombre', $category->nombre) }}" required>
                    @error('nombre') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Tipo</span>
                    <select name="tipo" required>
                        @foreach ($tipoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo', $category->tipo) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipo') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        @foreach ($estadoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $category->estado) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Orden</span>
                    <input type="number" name="orden" min="0" max="9999" value="{{ old('orden', $category->orden) }}" required>
                    @error('orden') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">
                    {{ $category->exists ? 'Guardar cambios' : 'Crear categoria' }}
                </button>
            </div>
        </form>
    </section>
@endsection
