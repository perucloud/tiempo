@extends('layouts.admin')

@section('title', $client->exists ? 'Editar cliente' : 'Nuevo cliente')
@section('eyebrow', 'Base de clientes')
@section('page-title', $client->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $client->exists ? 'Actualizar datos del cliente' : 'Crear cliente' }}</h2>
                <p>El cliente compra desde `/app`; esta vista es para consulta y soporte operativo de TIEMPO.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.clients.index') }}">Volver</a>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Nombres</span>
                    <input type="text" name="nombres" value="{{ old('nombres', $client->nombres) }}" required>
                    @error('nombres') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Apellidos</span>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $client->apellidos) }}">
                    @error('apellidos') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Telefono</span>
                    <input type="text" name="telefono" value="{{ old('telefono', $client->telefono) }}" required>
                    @error('telefono') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}">
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Documento</span>
                    <input type="text" name="documento" value="{{ old('documento', $client->documento) }}">
                    @error('documento') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        @foreach ($estadoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $client->estado) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">
                    {{ $client->exists ? 'Guardar cambios' : 'Crear cliente' }}
                </button>
            </div>
        </form>
    </section>
@endsection
