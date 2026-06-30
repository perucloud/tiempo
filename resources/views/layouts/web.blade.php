<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'TIEMPO Delivery conecta clientes, negocios afiliados y repartidores con una operacion centralizada de delivery.')">
    <title>@yield('title', 'TIEMPO Delivery')</title>
    <link rel="stylesheet" href="{{ asset('css/web.css') }}">
</head>
<body>
    <header class="web-header">
        <nav class="web-nav" aria-label="Navegacion principal">
            <a class="web-brand" href="{{ route('home') }}">
                <span class="web-brand-mark">T</span>
                <span>TIEMPO Delivery</span>
            </a>

            <div class="web-nav-links">
                <a href="#clientes">Clientes</a>
                <a href="#negocios">Negocios afiliados</a>
                <a href="#operacion">Operacion</a>
                <a class="web-button web-button-outline" href="{{ route('admin.login') }}">Admin</a>
            </div>
        </nav>
    </header>

    @yield('content')

    <footer class="web-footer">
        <div class="web-section-inner">
            <span>TIEMPO Delivery</span>
            <span>Landing publica | App clientes | Dashboard operativo</span>
        </div>
    </footer>
</body>
</html>
