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
        <div class="admin-alert"><i class="bi bi-check-circle-fill"></i> {{ session('status') }}</div>
    @endif
    @if (session('status_error'))
        <div class="admin-alert admin-alert-error"><i class="bi bi-exclamation-circle-fill"></i> {{ session('status_error') }}</div>
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
                        @php
                            $isPrimary = $user->isPrimary();
                            $isSelf    = $user->id === auth()->id();
                            $canBlock  = ! $isPrimary && ! $isSelf;
                            $canDelete = ! $isPrimary && ! $isSelf;
                        @endphp
                        <td>
                            <div class="adm-actions">
                                {{-- Editar --}}
                                <a class="adm-action-btn adm-action-edit"
                                   href="{{ route('admin.users.edit', $user) }}"
                                   title="Editar usuario">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                {{-- Bloquear / Activar --}}
                                @if ($canBlock)
                                    <form method="POST"
                                          action="{{ route('admin.users.toggle-status', $user) }}"
                                          style="margin:0;"
                                          data-type="{{ $user->status === 'activo' ? 'block' : 'unblock' }}"
                                          data-user="{{ $user->name }}">
                                        @csrf @method('PATCH')
                                        <button type="button"
                                                class="adm-action-btn {{ $user->status === 'activo' ? 'adm-action-block' : 'adm-action-unblock' }}"
                                                title="{{ $user->status === 'activo' ? 'Bloquear usuario' : 'Activar usuario' }}"
                                                onclick="openConfirmModal(this.closest('form'))">
                                            <i class="bi {{ $user->status === 'activo' ? 'bi-slash-circle-fill' : 'bi-check-circle-fill' }}"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="adm-action-btn adm-action-disabled"
                                          title="{{ $isPrimary ? 'SuperAdmin principal protegido' : 'No puedes bloquearte a ti mismo' }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </span>
                                @endif

                                {{-- Eliminar --}}
                                @if ($canDelete)
                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          style="margin:0;"
                                          data-type="delete"
                                          data-user="{{ $user->name }}">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="adm-action-btn adm-action-delete"
                                                title="Eliminar usuario"
                                                onclick="openConfirmModal(this.closest('form'))">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="adm-action-btn adm-action-disabled"
                                          title="{{ $isPrimary ? 'SuperAdmin principal protegido' : 'No puedes eliminarte a ti mismo' }}">
                                        <i class="bi bi-trash3"></i>
                                    </span>
                                @endif
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

                {{-- Módulos permitidos ─────────────────────────────── --}}
                <div class="adm-modules-section" id="u-modules-section" style="display:none;">
                    <div class="adm-modules-header">
                        <span class="adm-modules-label">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            Módulos de acceso
                        </span>
                        <div class="adm-modules-actions">
                            <button type="button" class="adm-modules-toggle-all" onclick="toggleAllModules(true)">
                                Seleccionar todos
                            </button>
                            <button type="button" class="adm-modules-toggle-all" onclick="toggleAllModules(false)">
                                Limpiar
                            </button>
                        </div>
                    </div>

                    <div id="u-superadmin-badge" style="display:none;">
                        <div class="adm-modules-full-access">
                            <i class="bi bi-shield-fill-check"></i>
                            SuperAdmin tiene acceso completo a todos los módulos del sistema.
                        </div>
                    </div>

                    <div class="adm-modules-grid" id="u-modules-grid">
                        @foreach ($assignableModules as $module)
                            <label class="adm-module-check">
                                <input type="checkbox"
                                       name="module_permissions[]"
                                       value="{{ $module['key'] }}"
                                       {{ in_array($module['key'], old('module_permissions', [])) ? 'checked' : '' }}>
                                <div class="adm-module-check-icon">
                                    <i class="bi {{ $module['icon'] }}"></i>
                                </div>
                                <span class="adm-module-check-name">{{ $module['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

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

{{-- ── Modal: Confirmación de acción ────────────────────────────────── --}}
<div class="adm-modal-overlay"
     id="confirmModal"
     style="display:none;"
     role="dialog"
     aria-modal="true"
     aria-labelledby="confirmModalTitle">

    <div class="adm-modal adm-confirm-modal">

        {{-- Sin header: el ícono y título van en el body --}}
        <div class="adm-confirm-body">
            <div class="adm-confirm-icon-wrap" id="confirmIconWrap">
                <i class="bi" id="confirmIconEl"></i>
            </div>
            <p class="adm-confirm-title" id="confirmModalTitle"></p>
            <p class="adm-confirm-desc" id="confirmDescEl"></p>
        </div>

        <div class="adm-confirm-footer">
            <button type="button"
                    class="admin-button admin-button-logout"
                    id="confirmCancelBtn">
                <i class="bi bi-x"></i> Cancelar
            </button>
            <button type="button"
                    class="admin-button"
                    id="confirmActionBtn">
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Modal: Nuevo usuario ──────────────────────────────────────── */

    var userOverlay = document.getElementById('userModal');
    var hasErrors   = {{ $errors->any() ? 'true' : 'false' }};

    function openUserModal() {
        userOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        var firstError = userOverlay.querySelector('.has-error');
        setTimeout(function () {
            (firstError || document.getElementById('u-name')).focus();
        }, 50);
    }
    function closeUserModal() {
        userOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    if (hasErrors) openUserModal();

    userOverlay.addEventListener('click', function (e) {
        if (e.target === userOverlay) closeUserModal();
    });

    var eyeBtn  = document.getElementById('u-eye-btn');
    var pwdInp  = document.getElementById('u-password');
    var eyeIcon = document.getElementById('u-eye-icon');
    if (eyeBtn) {
        eyeBtn.addEventListener('click', function () {
            var show = pwdInp.type === 'password';
            pwdInp.type       = show ? 'text' : 'password';
            eyeIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            eyeBtn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    // ── Sección de módulos según el rol seleccionado ────────────────
    var roleSelect      = document.getElementById('u-role');
    var modulesSection  = document.getElementById('u-modules-section');
    var superBadge      = document.getElementById('u-superadmin-badge');
    var modulesGrid     = document.getElementById('u-modules-grid');
    var moduleActions   = modulesSection ? modulesSection.querySelector('.adm-modules-actions') : null;

    function onRoleChange() {
        var role = roleSelect.value;
        var needsModules = role === 'admin' || role === 'operador';
        var isSuperAdmin = role === 'superadmin';

        modulesSection.style.display = (needsModules || isSuperAdmin) ? 'block' : 'none';

        if (isSuperAdmin) {
            superBadge.style.display    = 'block';
            modulesGrid.style.display   = 'none';
            if (moduleActions) moduleActions.style.display = 'none';
        } else {
            superBadge.style.display    = 'none';
            modulesGrid.style.display   = 'grid';
            if (moduleActions) moduleActions.style.display = 'flex';
        }
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', onRoleChange);
        onRoleChange();
    }

    window.toggleAllModules = function (state) {
        if (!modulesGrid) return;
        modulesGrid.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
            cb.checked = state;
        });
    };

    window.openUserModal  = openUserModal;
    window.closeUserModal = closeUserModal;

    /* ── Modal: Confirmación de acción ────────────────────────────── */

    var confirmOverlay  = document.getElementById('confirmModal');
    var confirmIconWrap = document.getElementById('confirmIconWrap');
    var confirmIconEl   = document.getElementById('confirmIconEl');
    var confirmTitle    = document.getElementById('confirmModalTitle');
    var confirmDesc     = document.getElementById('confirmDescEl');
    var confirmActionBtn = document.getElementById('confirmActionBtn');
    var confirmCancelBtn = document.getElementById('confirmCancelBtn');
    var pendingForm     = null;

    var ACTION_CONFIG = {
        block: {
            wrapClass : 'adm-confirm-icon-wrap--amber',
            icon      : 'bi-slash-circle-fill',
            title     : '¿Bloquear usuario?',
            desc      : function (name) {
                return '<strong>' + name + '</strong> no podrá acceder al sistema mientras esté bloqueado.';
            },
            btnClass  : 'adm-confirm-btn--amber',
            btnIcon   : 'bi-slash-circle',
            btnText   : 'Sí, bloquear',
        },
        unblock: {
            wrapClass : 'adm-confirm-icon-wrap--green',
            icon      : 'bi-check-circle-fill',
            title     : '¿Activar usuario?',
            desc      : function (name) {
                return '<strong>' + name + '</strong> recuperará acceso completo al sistema.';
            },
            btnClass  : 'adm-confirm-btn--green',
            btnIcon   : 'bi-check-circle',
            btnText   : 'Sí, activar',
        },
        delete: {
            wrapClass : 'adm-confirm-icon-wrap--red',
            icon      : 'bi-trash3-fill',
            title     : '¿Eliminar usuario?',
            desc      : function (name) {
                return 'Estás a punto de eliminar a <strong>' + name + '</strong>. Esta acción <strong>no se puede deshacer</strong>.';
            },
            btnClass  : 'adm-confirm-btn--red',
            btnIcon   : 'bi-trash3',
            btnText   : 'Sí, eliminar',
        },
    };

    function openConfirmModal(form) {
        var type = form.dataset.type;
        var name = form.dataset.user;
        var cfg  = ACTION_CONFIG[type];
        if (!cfg) return;

        pendingForm = form;

        // Ícono
        confirmIconWrap.className = 'adm-confirm-icon-wrap ' + cfg.wrapClass;
        confirmIconEl.className   = 'bi ' + cfg.icon;

        // Textos
        confirmTitle.textContent  = cfg.title;
        confirmDesc.innerHTML     = cfg.desc(name);

        // Botón de acción
        confirmActionBtn.className = 'admin-button ' + cfg.btnClass;
        confirmActionBtn.innerHTML = '<i class="bi ' + cfg.btnIcon + '"></i> ' + cfg.btnText;

        confirmOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(function () { confirmActionBtn.focus(); }, 50);
    }

    function closeConfirmModal() {
        confirmOverlay.style.display = 'none';
        document.body.style.overflow = '';
        pendingForm = null;
    }

    confirmActionBtn.addEventListener('click', function () {
        if (pendingForm) {
            closeConfirmModal();
            pendingForm.submit();
        }
    });
    confirmCancelBtn.addEventListener('click', closeConfirmModal);
    confirmOverlay.addEventListener('click', function (e) {
        if (e.target === confirmOverlay) closeConfirmModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (confirmOverlay.style.display === 'flex') { closeConfirmModal(); return; }
            if (userOverlay.style.display    === 'flex') { closeUserModal();    return; }
        }
    });

    window.openConfirmModal  = openConfirmModal;
    window.closeConfirmModal = closeConfirmModal;

})();
</script>
@endpush
