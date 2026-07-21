@extends('layouts.app-auth')

@section('title', 'Bienvenido a TIEMPO Delivery')

@section('content')

{{-- Alerta de pedido no encontrado (flash desde OrderTrackingController) --}}
@if(session('order_error'))
    <div class="auth-error-box" role="alert">{{ session('order_error') }}</div>
@endif

<div class="auth-welcome">

    {{-- Logo --}}
    <div class="auth-logo-wrap">
        <div class="auth-logo-icon">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="24" fill="#0f766e"/>
                <path d="M24 12C17.373 12 12 17.373 12 24s5.373 12 12 12 12-5.373 12-12S30.627 12 24 12zm0 3a1.5 1.5 0 0 1 1.5 1.5V24a1.5 1.5 0 0 1-.44 1.06l-4 4a1.5 1.5 0 1 1-2.12-2.12L22.5 23.38V16.5A1.5 1.5 0 0 1 24 15z" fill="#fff"/>
            </svg>
        </div>
        <h1 class="auth-logo-name">TIEMPO</h1>
        <p class="auth-logo-tagline">Delivery rápido en tu zona</p>
    </div>

    {{-- Ilustración / hero --}}
    <div class="auth-hero-illustration">
        <div class="auth-hero-circles">
            <div class="auth-hero-c1"></div>
            <div class="auth-hero-c2"></div>
            <div class="auth-hero-c3"></div>
        </div>
        <span class="auth-hero-emoji">🛵</span>
    </div>

    {{-- CTA --}}
    <div class="auth-welcome-cta">
        <h2>¿Listo para pedir?</h2>
        <p>Crea tu cuenta en segundos y disfruta de los mejores restaurantes y tiendas de tu zona.</p>

        <a class="auth-btn auth-btn-primary" href="{{ route('app.registro') }}">
            Darme de alta en TIEMPO
        </a>

        <a class="auth-btn auth-btn-outline" href="{{ route('app.login') }}">
            Ya tengo cuenta — Iniciar sesión
        </a>

        {{-- Google OAuth (preparado, activa cuando tengas credenciales) --}}
        {{-- <a class="auth-btn auth-btn-social auth-btn-google" href="{{ route('app.auth.google') }}">
            <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
            Continuar con Google
        </a> --}}

        {{-- Facebook OAuth (preparado) --}}
        {{-- <a class="auth-btn auth-btn-social auth-btn-facebook" href="{{ route('app.auth.facebook') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.027 4.388 11.024 10.125 11.927v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796v8.437C19.612 23.097 24 18.1 24 12.073z"/></svg>
            Continuar con Facebook
        </a> --}}

        <p class="auth-terms">
            Al registrarte aceptas nuestros
            <a href="#">Términos de uso</a> y
            <a href="#">Política de privacidad</a>.
        </p>
    </div>

</div>

@endsection
