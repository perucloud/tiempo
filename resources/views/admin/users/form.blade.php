@extends('layouts.admin')

@section('title', $userModel->exists ? 'Editar usuario' : 'Nuevo usuario')
@section('eyebrow', 'Seguridad y accesos')
@section('page-title', $userModel->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $userModel->exists ? 'Actualizar acceso' : 'Crear acceso' }}</h2>
                <p>Los roles definen el alcance de cada usuario dentro de TIEMPO.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.users.index') }}">Volver</a>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Nombre</span>
                    <input type="text" name="name" value="{{ old('name', $userModel->name) }}" required>
                    @error('name') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', $userModel->email) }}" required>
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Rol</span>
                    <select name="role" required>
                        @foreach ($roleOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $userModel->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Estado</span>
                    <select name="status" required>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $userModel->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Contrasena</span>
                    <input type="password" name="password" @required(! $userModel->exists)>
                    <small>{{ $userModel->exists ? 'Dejar vacio para mantener la actual.' : 'Minimo 8 caracteres.' }}</small>
                    @error('password') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">
                    {{ $userModel->exists ? 'Guardar cambios' : 'Crear usuario' }}
                </button>
            </div>
        </form>
    </section>
@endsection
