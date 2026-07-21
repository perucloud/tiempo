@extends('layouts.admin-auth')

@section('title', 'Nueva contraseña')

@section('content')

{{-- Logo ──────────────────────────────────────────────────────────── --}}
<div class="al-login-logo">
    <img src="{{ asset('images/dashboard/tiempologo.png') }}" alt="TIEMPO Delivery" draggable="false">
</div>

{{-- Cabecera ────────────────────────────────────────────────────────── --}}
<div class="al-form-header">
    <h1>Nueva contraseña</h1>
    <p>Elige una contraseña segura para tu cuenta.</p>
</div>

{{-- Error ───────────────────────────────────────────────────────────── --}}
@if ($errors->any())
    <div class="al-alert-error" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('admin.password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    {{-- Email ──────────────────────────────────────────────────────── --}}
    <div class="al-field">
        <label class="al-label" for="email">Correo electrónico</label>
        <div class="al-input-wrap">
            <i class="bi bi-envelope al-input-icon"></i>
            <input
                id="email"
                name="email"
                type="email"
                class="al-input {{ $errors->has('email') ? 'has-error' : '' }}"
                value="{{ old('email', request('email')) }}"
                autocomplete="email"
                required
                autofocus
            >
        </div>
    </div>

    {{-- Nueva contraseña ────────────────────────────────────────────── --}}
    <div class="al-field">
        <label class="al-label" for="password">Nueva contraseña</label>
        <div class="al-input-wrap">
            <i class="bi bi-lock al-input-icon"></i>
            <input
                id="password"
                name="password"
                type="password"
                class="al-input {{ $errors->has('password') ? 'has-error' : '' }}"
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                required
            >
            <button type="button" class="al-eye-btn" id="toggle-pass" aria-label="Mostrar contraseña">
                <i class="bi bi-eye" id="eye-pass"></i>
            </button>
        </div>
        @error('password')
            <p class="al-error-msg"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
        @enderror
    </div>

    {{-- Confirmar contraseña ─────────────────────────────────────────── --}}
    <div class="al-field">
        <label class="al-label" for="password_confirmation">Confirmar contraseña</label>
        <div class="al-input-wrap">
            <i class="bi bi-lock-fill al-input-icon"></i>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="al-input"
                autocomplete="new-password"
                placeholder="Repite la contraseña"
                required
            >
        </div>
    </div>

    <button type="submit" class="al-btn-submit" style="margin-top: 8px;">
        <i class="bi bi-shield-check"></i>
        Guardar nueva contraseña
    </button>
</form>

<div class="al-form-footer">
    <a href="{{ route('admin.login') }}" class="al-back-link">
        <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
    </a>
</div>

<script>
(function () {
    const btn  = document.getElementById('toggle-pass');
    const inp  = document.getElementById('password');
    const icon = document.getElementById('eye-pass');
    if (!btn) return;
    btn.addEventListener('click', function () {
        const show = inp.type === 'password';
        inp.type       = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
})();
</script>

@endsection
