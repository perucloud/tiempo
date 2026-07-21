@extends('layouts.admin-auth')

@section('title', 'Recuperar contraseña')

@section('content')

{{-- Logo ──────────────────────────────────────────────────────────── --}}
<div class="al-login-logo">
    <img src="{{ asset('images/dashboard/tiempologo.png') }}" alt="TIEMPO Delivery" draggable="false">
</div>

{{-- Cabecera ────────────────────────────────────────────────────────── --}}
<div class="al-form-header">
    <h1>Recuperar contraseña</h1>
    <p>Ingresa tu correo y te enviamos un enlace para restablecerla.</p>
</div>

{{-- Éxito ───────────────────────────────────────────────────────────── --}}
@if (session('status'))
    <div class="al-alert-success" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('status') }}</span>
    </div>
@endif

{{-- Error ───────────────────────────────────────────────────────────── --}}
@if ($errors->any())
    <div class="al-alert-error" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('admin.password.email') }}" novalidate>
    @csrf

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

    <button type="submit" class="al-btn-submit">
        <i class="bi bi-send"></i>
        Enviar enlace de recuperación
    </button>
</form>

<div class="al-form-footer">
    <a href="{{ route('admin.login') }}" class="al-back-link">
        <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
    </a>
</div>

@endsection
