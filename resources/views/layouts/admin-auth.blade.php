<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Acceso') | TIEMPO Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', system-ui, sans-serif; min-height: 100vh; background: #f1f5f9; -webkit-font-smoothing: antialiased; }
        a { color: inherit; text-decoration: none; }

        /* ── Shell split ── */
        .al-shell {
            display: grid;
            grid-template-columns: 480px 1fr;
            min-height: 100vh;
        }

        /* ── Panel izquierdo — marca ── */
        .al-brand {
            background: #0f172a;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
        }

        /* Decoración de fondo */
        .al-brand::before {
            content: '';
            position: absolute;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.18) 0%, transparent 70%);
            top: -120px;
            left: -120px;
            pointer-events: none;
        }
        .al-brand::after {
            content: '';
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.12) 0%, transparent 70%);
            bottom: -80px;
            right: -80px;
            pointer-events: none;
        }

        /* Logo */
        .al-logo {
            position: relative;
            z-index: 1;
        }
        .al-logo img {
            height: 48px;
            width: auto;
            max-width: 260px;
            object-fit: contain;
            /* Convierte el logo negro/naranja a blanco sobre fondo oscuro */
            filter: brightness(0) invert(1);
            user-select: none;
            draggable: none;
        }

        /* Centro — claim */
        .al-brand-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        .al-brand-eyebrow {
            font-size: 11px;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 18px;
        }
        .al-brand-title {
            font-size: 36px;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }
        .al-brand-desc {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.7;
            max-width: 340px;
        }

        /* Pills de módulos */
        .al-modules {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 40px;
        }
        .al-module-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12px;
            color: #94a3b8;
        }
        .al-module-pill i { color: #2563eb; font-size: 13px; }

        /* Footer marca */
        .al-brand-footer {
            font-size: 12px;
            color: #334155;
            position: relative;
            z-index: 1;
        }

        /* ── Panel derecho — formulario ── */
        .al-form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            background: #f8fafc;
        }
        .al-form-wrap {
            width: 100%;
            max-width: 420px;
        }

        /* Cabecera form */
        .al-form-header {
            margin-bottom: 40px;
        }
        .al-form-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.8px;
            margin-bottom: 6px;
        }
        .al-form-header p {
            font-size: 14px;
            color: #64748b;
        }

        /* Campos */
        .al-field {
            margin-bottom: 20px;
        }
        .al-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }
        .al-input-wrap {
            position: relative;
        }
        .al-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }
        .al-input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #0f172a;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .al-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .al-input.has-error {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,.10);
        }
        .al-eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: 17px;
            padding: 4px;
            line-height: 1;
            transition: color .15s;
        }
        .al-eye-btn:hover { color: #475569; }

        /* Error inline */
        .al-error-msg {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 13px;
            color: #dc2626;
            font-weight: 500;
        }
        .al-error-msg i { font-size: 14px; }

        /* Alert error general */
        .al-alert-error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #991b1b;
        }
        .al-alert-error i { font-size: 18px; color: #dc2626; flex-shrink: 0; margin-top: 1px; }

        /* Remember row */
        .al-remember {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 28px;
            cursor: pointer;
            user-select: none;
        }
        .al-remember input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #2563eb;
            cursor: pointer;
        }
        .al-remember span {
            font-size: 13px;
            color: #475569;
        }

        /* Botón submit */
        .al-btn-submit {
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .15s, transform .1s, box-shadow .15s;
            letter-spacing: -0.2px;
        }
        .al-btn-submit:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 14px rgba(37,99,235,.35);
        }
        .al-btn-submit:active { transform: scale(.98); }

        /* Footer form */
        .al-form-footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        /* ── Logo en formulario ── */
        .al-login-logo {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 28px;
        }
        .al-login-logo img {
            height: 46px;
            width: auto;
            max-width: 220px;
            object-fit: contain;
            user-select: none;
        }

        /* ── Label row (label + forgot link) ── */
        .al-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 7px;
        }
        .al-label-row .al-label { margin-bottom: 0; }
        .al-forgot-link {
            font-size: 12px;
            color: #2563eb;
            font-weight: 500;
            transition: color .15s;
        }
        .al-forgot-link:hover { color: #1d4ed8; text-decoration: underline; }

        /* ── Alert success ── */
        .al-alert-success {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #166534;
        }
        .al-alert-success i { font-size: 18px; color: #16a34a; flex-shrink: 0; margin-top: 1px; }

        /* ── reCAPTCHA ── */
        .al-captcha-wrap {
            margin-bottom: 24px;
        }
        .al-captcha-wrap .g-recaptcha {
            transform-origin: left top;
        }
        @media (max-width: 400px) {
            .al-captcha-wrap .g-recaptcha {
                transform: scale(.85);
            }
        }

        /* ── Back link ── */
        .al-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #2563eb;
            font-weight: 500;
            transition: color .15s;
        }
        .al-back-link:hover { color: #1d4ed8; text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .al-shell { grid-template-columns: 1fr; }
            .al-brand { display: none; }
            .al-form-panel {
                background: #fff;
                padding: 40px 24px;
                align-items: flex-start;
                padding-top: 64px;
            }
            .al-form-wrap { max-width: 100%; }
        }

        @media (max-width: 1200px) and (min-width: 901px) {
            .al-shell { grid-template-columns: 380px 1fr; }
            .al-brand { padding: 40px 36px; }
            .al-brand-title { font-size: 28px; }
        }
    </style>
</head>
<body>
<div class="al-shell">

    {{-- ── Panel izquierdo: marca ── --}}
    <div class="al-brand">
        <div class="al-logo">
            <img src="{{ asset('images/dashboard/tiempologo.png') }}" alt="TIEMPO Delivery" draggable="false">
        </div>

        <div class="al-brand-body">
            <p class="al-brand-eyebrow">Panel de operaciones</p>
            <h2 class="al-brand-title">Gestiona tu operación en tiempo real</h2>
            <p class="al-brand-desc">
                Pedidos, repartidores, negocios afiliados, pagos y reportes
                desde un único panel de control.
            </p>

            <div class="al-modules">
                <div class="al-module-pill"><i class="bi bi-box-seam"></i> Pedidos</div>
                <div class="al-module-pill"><i class="bi bi-bicycle"></i> Repartidores</div>
                <div class="al-module-pill"><i class="bi bi-shop"></i> Negocios</div>
                <div class="al-module-pill"><i class="bi bi-credit-card"></i> Pagos</div>
                <div class="al-module-pill"><i class="bi bi-bar-chart-line"></i> Reportes</div>
                <div class="al-module-pill"><i class="bi bi-geo-alt"></i> Zonas</div>
            </div>
        </div>

        <div class="al-brand-footer">
            &copy; {{ date('Y') }} TIEMPO Delivery &mdash; Todos los derechos reservados
        </div>
    </div>

    {{-- ── Panel derecho: contenido ── --}}
    <div class="al-form-panel">
        <div class="al-form-wrap">
            @yield('content')
        </div>
    </div>

</div>
</body>
</html>
