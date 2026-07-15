<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'Turno') | TIEMPO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/courier.css') }}">
</head>
<body>
    <div class="courier-shell">
        <header class="courier-header">
            <span class="courier-brand">
                <span class="courier-brand-mark">T</span>
                TIEMPO
            </span>
            <span class="courier-header-badge">Repartidor</span>
        </header>

        <main class="courier-main">
            @yield('content')
        </main>

        <footer class="courier-footer">
            <span>TIEMPO Delivery &copy; {{ date('Y') }}</span>
        </footer>
    </div>

    @stack('courier_scripts')
</body>
</html>
