<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Delivery local de comidas y bebidas. Pide de tus restaurantes favoritos y recíbelo a tiempo.')">
    <title>@yield('title', 'Tiempo Delivery')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tiempo-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web.css') }}">
    @stack('web_styles')
</head>
<body>
    @yield('content')
    @stack('web_scripts')
</body>
</html>
