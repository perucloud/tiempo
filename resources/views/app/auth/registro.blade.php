@extends('layouts.app-auth')

@section('title', 'Crear cuenta — TIEMPO')

@section('content')

<div class="auth-card">

    <a class="auth-back" href="{{ route('app.home') }}" aria-label="Volver">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>

    <div class="auth-card-header">
        <span class="auth-card-logo">T</span>
        <h1>Crear cuenta</h1>
        <p>Únete a TIEMPO y pide en segundos</p>
    </div>

    @if($errors->any())
        <div class="auth-error-box">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('app.registro.post') }}">
        @csrf

        <div class="auth-field">
            <label for="nombres">Nombre completo *</label>
            <input id="nombres" name="nombres" type="text"
                   value="{{ old('nombres') }}"
                   placeholder="Tu nombre" autocomplete="name" required>
        </div>

        <div class="auth-field">
            <label for="telefono">Número de celular *</label>
            <div class="auth-input-group">
                <span class="auth-prefix">+51</span>
                <input id="telefono" name="telefono" type="tel"
                       value="{{ old('telefono') }}"
                       placeholder="9XXXXXXXX" maxlength="15"
                       inputmode="tel" autocomplete="tel" required>
            </div>
            <small class="auth-hint">Lo usarás para iniciar sesión</small>
        </div>

        <div class="auth-field">
            <label for="password">Contraseña *</label>
            <div class="auth-input-group">
                <input id="password" name="password" type="password"
                       placeholder="Mín. 8 caracteres" autocomplete="new-password" required minlength="8">
                <button type="button" class="auth-eye-btn" data-target="password" aria-label="Mostrar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <div class="auth-pwd-strength" id="pwd-strength"></div>
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Confirmar contraseña *</label>
            <div class="auth-input-group">
                <input id="password_confirmation" name="password_confirmation" type="password"
                       placeholder="Repite tu contraseña" autocomplete="new-password" required>
                <button type="button" class="auth-eye-btn" data-target="password_confirmation" aria-label="Mostrar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <label class="auth-check-label">
            <input type="checkbox" name="terminos" value="1" required>
            <span>Acepto los <a href="#" target="_blank">Términos de uso</a> y la <a href="#" target="_blank">Política de privacidad</a></span>
        </label>

        <button class="auth-btn auth-btn-primary" type="submit">
            Darme de alta en TIEMPO →
        </button>

    </form>

    {{-- Social (preparado) --}}
    <div class="auth-divider"><span>o continúa con</span></div>
    <div class="auth-social-btns">
        <button class="auth-btn auth-btn-social auth-btn-google" type="button" disabled title="Próximamente">
            <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
            Continuar con Google
        </button>
        <button class="auth-btn auth-btn-social auth-btn-facebook" type="button" disabled title="Próximamente">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.027 4.388 11.024 10.125 11.927v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796v8.437C19.612 23.097 24 18.1 24 12.073z"/></svg>
            Continuar con Facebook
        </button>
    </div>

    <p class="auth-switch">
        ¿Ya tienes cuenta?
        <a href="{{ route('app.login') }}">Iniciar sesión</a>
    </p>

</div>

@endsection

@push('app_scripts')
<script>
// Toggle password visibility
document.querySelectorAll('.auth-eye-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});

// Password strength indicator
const pwdInput = document.getElementById('password');
const pwdStrength = document.getElementById('pwd-strength');
if (pwdInput && pwdStrength) {
    pwdInput.addEventListener('input', () => {
        const v = pwdInput.value;
        let strength = 0;
        if (v.length >= 8)  strength++;
        if (/[A-Z]/.test(v)) strength++;
        if (/[0-9]/.test(v)) strength++;
        if (/[^A-Za-z0-9]/.test(v)) strength++;

        const labels = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'];
        const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
        pwdStrength.innerHTML = v.length > 0
            ? `<span style="color:${colors[strength]}">${labels[strength] || 'Débil'}</span>`
            : '';
    });
}
</script>
@endpush
