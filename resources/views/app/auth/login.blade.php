@extends('layouts.app-auth')

@section('title', 'Iniciar sesión — TIEMPO')

@section('content')

<div class="auth-card">

    <a class="auth-back" href="{{ route('app.home') }}" aria-label="Volver">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>

    <div class="auth-card-header">
        <span class="auth-card-logo">T</span>
        <h1>Iniciar sesión</h1>
        <p>Ingresa tu número de celular y contraseña</p>
    </div>

    @if($errors->any())
        <div class="auth-error-box">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('app.login.post') }}">
        @csrf

        <div class="auth-field">
            <label for="telefono">Número de celular</label>
            <div class="auth-input-group">
                <span class="auth-prefix">+51</span>
                <input id="telefono" name="telefono" type="tel"
                       value="{{ old('telefono') }}"
                       placeholder="9XXXXXXXX" maxlength="15"
                       inputmode="tel" autocomplete="tel" required>
            </div>
        </div>

        <div class="auth-field">
            <label for="password">
                Contraseña
                <a class="auth-forgot" href="{{ route('app.recuperar') }}">¿Olvidaste tu contraseña?</a>
            </label>
            <div class="auth-input-group">
                <input id="password" name="password" type="password"
                       placeholder="Tu contraseña" autocomplete="current-password" required>
                <button type="button" class="auth-eye-btn" data-target="password" aria-label="Mostrar contraseña">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <label class="auth-remember">
            <input type="checkbox" name="remember"> Mantenerme conectado
        </label>

        <button class="auth-btn auth-btn-primary" type="submit">Iniciar sesión</button>
    </form>

    <p class="auth-switch">
        ¿No tienes cuenta?
        <a href="{{ route('app.registro') }}">Regístrate gratis</a>
    </p>

</div>

@endsection

@push('app_scripts')
<script>
document.querySelectorAll('.auth-eye-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});
</script>
@endpush
