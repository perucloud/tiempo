@extends('layouts.admin-auth')

@section('title', 'Iniciar sesión')

@section('content')

{{-- Logo ──────────────────────────────────────────────────────────── --}}
<div class="al-login-logo">
    <img src="{{ asset('images/dashboard/tiempologo.png') }}" alt="TIEMPO Delivery" draggable="false">
</div>

{{-- Cabecera ────────────────────────────────────────────────────────── --}}
<div class="al-form-header">
    <h1>Bienvenido de nuevo</h1>
    <p>Ingresa tus credenciales para acceder al panel.</p>
</div>

{{-- Alert de error general ──────────────────────────────────────────── --}}
@if ($errors->has('email') || $errors->has('captcha'))
    <div class="al-alert-error" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>{{ $errors->first('email') ?: $errors->first('captcha') }}</span>
    </div>
@endif

{{-- Flash (reset password exitoso) ─────────────────────────────────── --}}
@if (session('status'))
    <div class="al-alert-success" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('status') }}</span>
    </div>
@endif

<form method="POST" action="{{ route('admin.login.store') }}" novalidate>
    @csrf

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
                value="{{ old('email') }}"
                autocomplete="email"
                placeholder="superadmin@tiempo.com.pe"
                required
                autofocus
            >
        </div>
    </div>

    {{-- Contraseña ──────────────────────────────────────────────────── --}}
    <div class="al-field">
        <div class="al-label-row">
            <label class="al-label" for="password">Contraseña</label>
            <a href="{{ route('admin.password.request') }}" class="al-forgot-link">
                ¿Olvidaste tu contraseña?
            </a>
        </div>
        <div class="al-input-wrap">
            <i class="bi bi-lock al-input-icon"></i>
            <input
                id="password"
                name="password"
                type="password"
                class="al-input {{ $errors->has('password') ? 'has-error' : '' }}"
                autocomplete="current-password"
                placeholder="••••••••"
                required
            >
            <button type="button" class="al-eye-btn" id="toggle-password" aria-label="Mostrar contraseña">
                <i class="bi bi-eye" id="eye-icon"></i>
            </button>
        </div>
    </div>

    {{-- reCAPTCHA ───────────────────────────────────────────────────── --}}
    @if(config('services.recaptcha.key'))
        <div class="al-captcha-wrap">
            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
            @error('captcha')
                <p class="al-error-msg"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- Submit ──────────────────────────────────────────────────────── --}}
    <button type="submit" class="al-btn-submit">
        <i class="bi bi-box-arrow-in-right"></i>
        Ingresar al panel
    </button>

    {{-- Recordar sesión — debajo del botón ─────────────────────────── --}}
    <label class="al-remember" style="justify-content: center; margin-top: 18px; margin-bottom: 0;">
        <input name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
        <span>Mantener sesión iniciada</span>
    </label>

</form>

<div class="al-form-footer">
    ¿Problemas para ingresar? Contacta al administrador del sistema.
</div>

@if(config('services.recaptcha.key'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<script>
(function () {
    const btn  = document.getElementById('toggle-password');
    const inp  = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (!btn) return;
    btn.addEventListener('click', function () {
        const show = inp.type === 'password';
        inp.type       = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        btn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
})();
</script>

@endsection
