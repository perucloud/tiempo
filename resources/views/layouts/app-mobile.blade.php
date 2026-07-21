<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-public-key" content="{{ config('webpush.public_key') }}">
    <meta name="push-subscribe-url" content="{{ route('app.push.subscribe') }}">
    <meta name="push-worker-url" content="{{ route('app.service-worker') }}">
    <meta name="theme-color" content="#0f766e">
    <meta name="description" content="@yield('description', 'App móvil de TIEMPO Delivery para clientes.')">
    <title>@yield('title', 'TIEMPO App')</title>
    <link rel="manifest" href="{{ asset('app/manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('app/icon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app-mobile.css') }}">
</head>
<body>
    <div class="mobile-shell">
        <main class="mobile-main">
            @yield('content')
        </main>

        @auth('cliente')
        <nav class="mobile-bottom-nav" aria-label="Navegación de la app">
            <a href="{{ route('app.inicio') }}" class="{{ request()->routeIs('app.inicio') ? 'is-active' : '' }}">
                <span class="nav-dot"></span>
                Inicio
            </a>
            <a href="{{ route('app.inicio') }}#buscar">
                <span class="nav-dot"></span>
                Buscar
            </a>
            <a href="{{ route('app.inicio') }}#carrito">
                <span class="nav-dot"></span>
                Carrito
            </a>
            <a href="{{ route('app.inicio') }}#pedidos">
                <span class="nav-dot"></span>
                Pedidos
            </a>
            <a href="{{ route('app.perfil') }}" class="{{ request()->routeIs('app.perfil') ? 'is-active' : '' }}">
                <span class="nav-dot"></span>
                Perfil
            </a>
        </nav>
        @endauth
    </div>

    <script src="{{ asset('js/app-mobile.js') }}" defer></script>
    <script src="{{ asset('js/push-notifications.js') }}" defer></script>
    @stack('app_scripts')
</body>
</html>
