@extends('layouts.admin')

@section('title', $zone->exists ? 'Editar zona' : 'Nueva zona')
@section('eyebrow', 'Zonas y tarifas')
@section('page-title', $zone->exists ? 'Editar zona de delivery' : 'Nueva zona de delivery')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $zone->exists ? 'Actualizar zona' : 'Crear zona' }}</h2>
                <p>Configura cobertura, costo de delivery y pedido minimo.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.settings.index') }}">Volver</a>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Nombre</span>
                    <input type="text" name="nombre" value="{{ old('nombre', $zone->nombre) }}" required>
                    @error('nombre') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Costo delivery</span>
                    <input type="number" name="costo_delivery" min="0" step="0.10" value="{{ old('costo_delivery', $zone->costo_delivery) }}" required>
                    @error('costo_delivery') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Pedido minimo</span>
                    <input type="number" name="pedido_minimo" min="0" step="0.10" value="{{ old('pedido_minimo', $zone->pedido_minimo) }}">
                    @error('pedido_minimo') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Estado</span>
                    <select name="activo">
                        <option value="1" @selected(old('activo', $zone->activo) == true)>Activa</option>
                        <option value="0" @selected(old('activo', $zone->activo) == false)>Inactiva</option>
                    </select>
                    @error('activo') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field admin-field-wide">
                    <span>Descripcion de cobertura</span>
                    <textarea name="descripcion_cobertura" rows="4">{{ old('descripcion_cobertura', $zone->descripcion_cobertura) }}</textarea>
                    @error('descripcion_cobertura') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">
                    {{ $zone->exists ? 'Guardar cambios' : 'Crear zona' }}
                </button>
            </div>
        </form>
    </section>
@endsection
