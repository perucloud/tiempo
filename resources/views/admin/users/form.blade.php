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
        <a class="admin-button admin-button-logout" href="{{ route('admin.users.index') }}">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="admin-alert admin-alert-error" style="margin-bottom:16px;">
            <i class="bi bi-exclamation-circle-fill"></i>
            Revisa los campos marcados y vuelve a intentarlo.
        </div>
    @endif

    <form class="admin-form" method="POST" action="{{ $action }}">
        @csrf
        @if ($method === 'PUT') @method('PUT') @endif

        <div class="admin-form-grid">

            {{-- Nombre --}}
            <label class="admin-field">
                <span>Nombre completo</span>
                <input type="text"
                       name="name"
                       value="{{ old('name', $userModel->name) }}"
                       placeholder="Ej. Ana García López"
                       required>
                @error('name') <small>{{ $message }}</small> @enderror
            </label>

            {{-- Email --}}
            <label class="admin-field">
                <span>Correo electrónico</span>
                <input type="email"
                       name="email"
                       value="{{ old('email', $userModel->email) }}"
                       placeholder="usuario@tiempo.com.pe"
                       required>
                @error('email') <small>{{ $message }}</small> @enderror
            </label>

            {{-- Rol --}}
            <label class="admin-field">
                <span>Rol</span>
                <select name="role" id="edit-role" required>
                    @foreach ($roleOptions as $value => $label)
                        <option value="{{ $value }}"
                                @selected(old('role', $userModel->role) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('role') <small>{{ $message }}</small> @enderror
            </label>

            {{-- Estado --}}
            <label class="admin-field">
                <span>Estado</span>
                <select name="status" required>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}"
                                @selected(old('status', $userModel->status) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status') <small>{{ $message }}</small> @enderror
            </label>

            {{-- Contraseña --}}
            <label class="admin-field admin-field-wide">
                <span>Contraseña</span>
                <input type="password"
                       name="password"
                       placeholder="{{ $userModel->exists ? 'Dejar vacío para mantener la actual' : 'Mínimo 8 caracteres' }}"
                       @required(! $userModel->exists)>
                @error('password')
                    <small>{{ $message }}</small>
                @else
                    @if($userModel->exists)
                        <small style="color:var(--admin-muted);">Dejar vacío para mantener la contraseña actual.</small>
                    @endif
                @enderror
            </label>

        </div>

        {{-- Sección de módulos ─────────────────────────────────────── --}}
        <div class="adm-modules-section" id="edit-modules-section">

            <div class="adm-modules-header">
                <span class="adm-modules-label">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    Módulos de acceso
                </span>
                <div class="adm-modules-actions" id="edit-module-actions">
                    <button type="button" class="adm-modules-toggle-all" onclick="editToggleAll(true)">
                        Seleccionar todos
                    </button>
                    <button type="button" class="adm-modules-toggle-all" onclick="editToggleAll(false)">
                        Limpiar
                    </button>
                </div>
            </div>

            <div id="edit-superadmin-badge" style="display:none;">
                <div class="adm-modules-full-access">
                    <i class="bi bi-shield-fill-check"></i>
                    SuperAdmin tiene acceso completo a todos los módulos del sistema.
                </div>
            </div>

            <div class="adm-modules-grid" id="edit-modules-grid">
                @php
                    $currentPerms = old('module_permissions', $userModel->module_permissions ?? []);
                @endphp
                @foreach ($assignableModules as $module)
                    <label class="adm-module-check">
                        <input type="checkbox"
                               name="module_permissions[]"
                               value="{{ $module['key'] }}"
                               {{ in_array($module['key'], $currentPerms) ? 'checked' : '' }}>
                        <div class="adm-module-check-icon">
                            <i class="bi {{ $module['icon'] }}"></i>
                        </div>
                        <span class="adm-module-check-name">{{ $module['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="admin-form-actions">
            <a class="admin-button admin-button-logout" href="{{ route('admin.users.index') }}">
                Cancelar
            </a>
            <button class="admin-button admin-button-primary" type="submit">
                <i class="bi {{ $userModel->exists ? 'bi-floppy' : 'bi-person-check' }}"></i>
                {{ $userModel->exists ? 'Guardar cambios' : 'Crear usuario' }}
            </button>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var roleSelect    = document.getElementById('edit-role');
    var modulesSection = document.getElementById('edit-modules-section');
    var superBadge    = document.getElementById('edit-superadmin-badge');
    var modulesGrid   = document.getElementById('edit-modules-grid');
    var moduleActions = document.getElementById('edit-module-actions');

    function onRoleChange() {
        var role = roleSelect.value;
        if (role === 'superadmin') {
            superBadge.style.display    = 'block';
            modulesGrid.style.display   = 'none';
            moduleActions.style.display = 'none';
        } else {
            superBadge.style.display    = 'none';
            modulesGrid.style.display   = 'grid';
            moduleActions.style.display = 'flex';
        }
    }

    roleSelect.addEventListener('change', onRoleChange);
    onRoleChange(); // estado inicial

    window.editToggleAll = function (state) {
        modulesGrid.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
            cb.checked = state;
        });
    };
})();
</script>
@endpush
