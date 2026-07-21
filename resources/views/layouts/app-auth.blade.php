<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f766e">
    <title>@yield('title', 'TIEMPO App')</title>
    <link rel="manifest" href="{{ asset('app/manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('app/icon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app-mobile.css') }}">
</head>
<body class="auth-body">
    <div class="auth-shell">
        @yield('content')
    </div>
    @stack('app_scripts')
</body>
</html>
