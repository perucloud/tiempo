@extends('layouts.admin')

@section('title', $courier->exists ? 'Editar repartidor' : 'Nuevo repartidor')
@section('eyebrow', 'Operacion de delivery')
@section('page-title', $courier->exists ? 'Editar repartidor' : 'Nuevo repartidor')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $courier->exists ? 'Actualizar datos del repartidor' : 'Crear repartidor' }}</h2>
                <p>Usa este modulo para controlar disponibilidad, datos de contacto y vehiculo.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.couriers.index') }}">Volver</a>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Nombres</span>
                    <input type="text" name="nombres" value="{{ old('nombres', $courier->nombres) }}" required>
                    @error('nombres') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Apellidos</span>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $courier->apellidos) }}">
                    @error('apellidos') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Telefono</span>
                    <input type="text" name="telefono" value="{{ old('telefono', $courier->telefono) }}" required>
                    @error('telefono') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Documento</span>
                    <input type="text" name="documento" value="{{ old('documento', $courier->documento) }}">
                    @error('documento') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Tipo de vehiculo</span>
                    <input type="text" name="vehiculo_tipo" value="{{ old('vehiculo_tipo', $courier->vehiculo_tipo) }}" placeholder="Moto, bicicleta, auto">
                    @error('vehiculo_tipo') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Placa</span>
                    <input type="text" name="vehiculo_placa" value="{{ old('vehiculo_placa', $courier->vehiculo_placa) }}">
                    @error('vehiculo_placa') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        @foreach ($estadoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $courier->estado) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">
                    {{ $courier->exists ? 'Guardar cambios' : 'Crear repartidor' }}
                </button>
            </div>
        </form>
    </section>
@endsection
