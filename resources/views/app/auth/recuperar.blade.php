@extends('layouts.app-auth')

@section('title', 'Recuperar acceso — TIEMPO')

@section('content')

<div class="auth-card">

    <a class="auth-back" href="{{ route('app.login') }}" aria-label="Volver">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>

    <div class="auth-card-header">
        <span class="auth-card-logo">🔑</span>
        <h1>Recuperar acceso</h1>
        <p>Te enviamos un código de verificación al celular</p>
    </div>

    {{-- Paso 1: Teléfono --}}
    <div id="step-phone">
        <div class="auth-field">
            <label for="rec-telefono">Número de celular</label>
            <div class="auth-input-group">
                <span class="auth-prefix">+51</span>
                <input id="rec-telefono" type="tel" placeholder="9XXXXXXXX" maxlength="15" inputmode="tel">
            </div>
        </div>
        <div id="step-phone-error" class="auth-error-box hidden"></div>
        <button class="auth-btn auth-btn-primary" id="btn-send-code">Enviar código</button>
    </div>

    {{-- Paso 2: Código OTP --}}
    <div id="step-otp" class="hidden">
        <p class="auth-otp-hint">Ingresa el código de 6 dígitos que enviamos a tu celular.</p>
        <div class="auth-field">
            <label for="rec-codigo">Código de verificación</label>
            <input id="rec-codigo" type="text" inputmode="numeric" pattern="[0-9]{6}"
                   maxlength="6" placeholder="000000" class="auth-otp-input">
        </div>
        <div id="step-otp-error" class="auth-error-box hidden"></div>
        <button class="auth-btn auth-btn-primary" id="btn-verify-code">Verificar código</button>
        <button class="auth-btn auth-btn-ghost" id="btn-resend">Reenviar código</button>
    </div>

    {{-- Paso 3: Nueva contraseña --}}
    <div id="step-password" class="hidden">
        <div class="auth-field">
            <label for="rec-password">Nueva contraseña</label>
            <div class="auth-input-group">
                <input id="rec-password" type="password" placeholder="Mín. 8 caracteres" minlength="8">
                <button type="button" class="auth-eye-btn" data-target="rec-password">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>
        <div class="auth-field">
            <label for="rec-password-confirm">Confirmar contraseña</label>
            <input id="rec-password-confirm" type="password" placeholder="Repite la contraseña">
        </div>
        <div id="step-pwd-error" class="auth-error-box hidden"></div>
        <button class="auth-btn auth-btn-primary" id="btn-reset-pwd">Cambiar contraseña</button>
    </div>

    {{-- Paso 4: Éxito --}}
    <div id="step-success" class="hidden auth-success-box">
        <span>✅</span>
        <p>¡Contraseña actualizada! Redirigiendo…</p>
    </div>

</div>

@endsection

@push('app_scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const show = id => document.getElementById(id).classList.remove('hidden');
const hide = id => document.getElementById(id).classList.add('hidden');
const showErr = (id, msg) => { const el = document.getElementById(id); el.innerHTML = `<p>${msg}</p>`; show(id); };

// Toggle eye
document.querySelectorAll('.auth-eye-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});

// Paso 1 → enviar código
document.getElementById('btn-send-code').addEventListener('click', async () => {
    const tel = document.getElementById('rec-telefono').value.trim();
    hide('step-phone-error');
    const res = await fetch('{{ route("app.recuperar.codigo") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ telefono: tel }),
    });
    const json = await res.json();
    if (!res.ok) { showErr('step-phone-error', json.message); return; }
    hide('step-phone');
    show('step-otp');
    if (json.debug_code) document.getElementById('rec-codigo').value = json.debug_code;
});

// Reenviar
document.getElementById('btn-resend').addEventListener('click', () => {
    hide('step-otp'); show('step-phone');
});

// Paso 2 → verificar OTP
document.getElementById('btn-verify-code').addEventListener('click', async () => {
    hide('step-otp-error');
    const tel = document.getElementById('rec-telefono').value.trim();
    const cod = document.getElementById('rec-codigo').value.trim();
    const res = await fetch('{{ route("app.recuperar.verificar") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ telefono: tel, codigo: cod }),
    });
    const json = await res.json();
    if (!res.ok) { showErr('step-otp-error', json.message); return; }
    hide('step-otp'); show('step-password');
});

// Paso 3 → nueva contraseña
document.getElementById('btn-reset-pwd').addEventListener('click', async () => {
    hide('step-pwd-error');
    const pwd  = document.getElementById('rec-password').value;
    const conf = document.getElementById('rec-password-confirm').value;
    if (pwd !== conf) { showErr('step-pwd-error', 'Las contraseñas no coinciden.'); return; }
    if (pwd.length < 8) { showErr('step-pwd-error', 'Mínimo 8 caracteres.'); return; }
    const res = await fetch('{{ route("app.recuperar.reset") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ password: pwd, password_confirmation: conf }),
    });
    const json = await res.json();
    if (!res.ok) { showErr('step-pwd-error', json.message); return; }
    hide('step-password'); show('step-success');
    setTimeout(() => window.location.href = json.redirect ?? '{{ route("app.inicio") }}', 1500);
});
</script>
@endpush
