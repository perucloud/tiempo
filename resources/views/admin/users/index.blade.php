@extends('layouts.admin')

@section('title', 'Usuarios')
@section('eyebrow', 'Seguridad y accesos')
@section('page-title', 'Gestión de usuarios')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-header">
        <div>
            <h2>Usuarios del sistema</h2>
            <p>Administra accesos para SuperAdmin, Admin, Operador, Negocio Afiliado, Repartidor y Cliente.</p>
        </div>
        <button class="admin-button admin-button-primary" type="button" onclick="openUserModal()">
            <i class="bi bi-person-plus"></i> Nuevo usuario
        </button>
    </div>

    @if (session('status'))
        <div class="admin-alert">{{ session('status') }}</div>
    @endif

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roleLabel() }}</td>
                        <td>
                            @if ($user->status === 'activo')
                                <span class="admin-badge admin-badge-green admin-badge--solid admin-badge--dot">Activo</span>
                            @else
                                <span class="admin-badge admin-badge-slate admin-badge--solid">Inactivo</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <div class="adm-actions">
                                {{-- Editar --}}
                                <a class="adm-action-btn adm-action-edit"
                                   href="{{ route('admin.users.edit', $user) }}"
                                   title="Editar usuario">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                {{-- Bloquear / Activar --}}
                                <form method="POST"
                                      action="{{ route('admin.users.toggle-status', $user) }}"
                                      style="margin:0;">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="adm-action-btn {{ $user->status === 'activo' ? 'adm-action-block' : 'adm-action-unblock' }}"
                                            title="{{ $user->status === 'activo' ? 'Bloquear usuario' : 'Activar usuario' }}">
                                        <i class="bi {{ $user->status === 'activo' ? 'bi-slash-circle-fill' : 'bi-check-circle-fill' }}"></i>
                                    </button>
                                </form>

                                {{-- Eliminar --}}
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user) }}"
                                      style="margin:0;"
                                      data-confirm="¿Eliminar a {{ addslashes($user->name) }}? Esta acción no se puede deshacer.">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="adm-action-btn adm-action-delete"
                                            title="Eliminar usuario">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color: var(--admin-muted); padding: 32px;">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">
        {{ $users->links() }}
    </div>
</section>

{{-- ── Modal: Nuevo usuario ───────────────────────────────────────────── --}}
<div class="adm-modal-overlay"
     id="userModal"
     style="display:none;"
     role="dialog"
     aria-modal="true"
     aria-labelledby="userModalTitle">

    <div class="adm-modal" id="userModalBox">

        {{-- Header ──────────────────────────────────────────────────── --}}
        <div class="adm-modal-header">
            <div class="adm-modal-title">
                <div class="adm-modal-title-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <span id="userModalTitle">Nuevo usuario</span>
            </div>
            <button type="button"
                    class="adm-modal-close"
                    onclick="closeUserModal()"
                    aria-label="Cerrar modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Body ────────────────────────────────────────────────────── --}}
        <div class="adm-modal-body">

            @if ($errors->any())
                <div class="adm-modal-alert" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>Revisa los campos marcados y vuelve a intentarlo.</span>
                </div>
            @endif

            <form id="userCreateForm"
                  method="POST"
                  action="{{ route('admin.users.store') }}"
                  novalidate>
                @csrf

                <div class="adm-modal-grid">

                    {{-- Nombre ──────────────────────────────────────── --}}
                    <div class="adm-modal-field adm-modal-field--full">
                        <label class="adm-modal-label" for="u-name">Nombre completo</label>
                        <div class="adm-modal-input-wrap">
                            <i class="bi bi-person adm-modal-input-icon"></i>
                            <input
                                id="u-name"
                                name="name"
                                type="text"
                                class="adm-modal-input {{ $errors->has('name') ? 'has-error' : '' }}"
                                value="{{ old('name') }}"
                                placeholder="Ej. Ana García López"
                                autocomplete="name"
                                required
                            >
                        </div>
                        @error('name')
                            <span class="adm-modal-error">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Email ───────────────────────────────────────── --}}
                    <div class="adm-modal-field adm-modal-field--full">
                        <label class="adm-modal-label" for="u-email">Correo electrónico</label>
                        <div class="adm-modal-input-wrap">
                            <i class="bi bi-envelope adm-modal-input-icon"></i>
                            <input
                                id="u-email"
                                name="email"
                                type="email"
                                class="adm-modal-input {{ $errors->has('email') ? 'has-error' : '' }}"
                                value="{{ old('email') }}"
                                placeholder="usuario@tiempo.com.pe"
                                autocomplete="email"
                                required
                            >
                        </div>
                        @error('email')
                            <span class="adm-modal-error">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Rol ─────────────────────────────────────────── --}}
                    <div class="adm-modal-field">
                        <label class="adm-modal-label" for="u-role">Rol</label>
                        <div class="adm-modal-input-wrap">
                            <i class="bi bi-shield-check adm-modal-input-icon"></i>
                            <select
                                id="u-role"
                                name="role"
                                class="adm-modal-input {{ $errors->has('role') ? 'has-error' : '' }}"
                                required
                            >
                                <option value="">Seleccionar rol…</option>
                                @foreach ($roleOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('role') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('role')
                            <span class="adm-modal-error">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Estado ──────────────────────────────────────── --}}
                    <div class="adm-modal-field">
                        <label class="adm-modal-label" for="u-status">Estado</label>
                        <div class="adm-modal-input-wrap">
                            <i class="bi bi-toggle-on adm-modal-input-icon"></i>
                            <select
                                id="u-status"
                                name="status"
                                class="adm-modal-input {{ $errors->has('status') ? 'has-error' : '' }}"
                                required
                            >
                                <option value="">Seleccionar estado…</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', 'activo') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('status')
                            <span class="adm-modal-error">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Contraseña ───────────────────────────────────── --}}
                    <div class="adm-modal-field adm-modal-field--full">
                        <label class="adm-modal-label" for="u-password">Contraseña</label>
                        <div class="adm-modal-input-wrap">
                            <i class="bi bi-lock adm-modal-input-icon"></i>
                            <input
                                id="u-password"
                                name="password"
                                type="password"
                                class="adm-modal-input {{ $errors->has('password') ? 'has-error' : '' }}"
                                placeholder="Mínimo 8 caracteres"
                                autocomplete="new-password"
                                required
                            >
                            <button type="button"
                                    class="adm-modal-eye"
                                    id="u-eye-btn"
                                    aria-label="Mostrar contraseña">
                                <i class="bi bi-eye" id="u-eye-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="adm-modal-error">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </span>
                        @else
                            <span class="adm-modal-hint">Mínimo 8 caracteres.</span>
                        @enderror
                    </div>

                </div>{{-- /.adm-modal-grid --}}
            </form>
        </div>{{-- /.adm-modal-body --}}

        {{-- Footer ──────────────────────────────────────────────────── --}}
        <div class="adm-modal-footer">
            <button type="button"
                    class="admin-button admin-button-logout"
                    onclick="closeUserModal()">
                Cancelar
            </button>
            <button type="submit"
                    form="userCreateForm"
                    class="admin-button admin-button-primary">
                <i class="bi bi-person-check"></i>
                Crear usuario
            </button>
        </div>

    </div>{{-- /.adm-modal --}}
</div>{{-- /.adm-modal-overlay --}}

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var overlay  = document.getElementById('userModal');
    var hasErrors = {{ $errors->any() ? 'true' : 'false' }};

    function openUserModal() {
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        // Foco al primer campo con error, o al nombre
        var firstError = overlay.querySelector('.has-error');
        setTimeout(function () {
            (firstError || document.getElementById('u-name')).focus();
        }, 50);
    }

    function closeUserModal() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Auto-abrir si hay errores de validación (store falló)
    if (hasErrors) openUserModal();

    // Cerrar al clic en el overlay (fuera del modal)
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeUserModal();
    });

    // Cerrar con Esc
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.style.display === 'flex') closeUserModal();
    });

    // Toggle mostrar/ocultar contraseña
    var eyeBtn  = document.getElementById('u-eye-btn');
    var pwdInp  = document.getElementById('u-password');
    var eyeIcon = document.getElementById('u-eye-icon');
    if (eyeBtn) {
        eyeBtn.addEventListener('click', function () {
            var show = pwdInp.type === 'password';
            pwdInp.type      = show ? 'text' : 'password';
            eyeIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            eyeBtn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    // Exponer al scope global para los atributos onclick del HTML
    window.openUserModal  = openUserModal;
    window.closeUserModal = closeUserModal;

    // Confirmación antes de eliminar
    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (window.confirm(form.dataset.confirm)) form.submit();
        });
    });
})();
</script>
@endpush
