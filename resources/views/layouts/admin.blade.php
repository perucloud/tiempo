<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | TIEMPO Delivery</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar" aria-label="Menu administrativo">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span class="admin-brand-mark">T</span>
                <span>
                    <strong>TIEMPO</strong>
                    <small>Delivery Ops</small>
                </span>
            </a>

            <nav class="admin-nav">
                @foreach ($adminModules ?? [] as $module)
                    <a class="admin-nav-link {{ $module['active'] ?? false ? 'is-active' : '' }}" href="{{ $module['url'] ?? '#' }}">
                        <span class="admin-nav-icon">{{ $module['icon'] }}</span>
                        <span>{{ $module['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div>
                    <p class="admin-eyebrow">@yield('eyebrow', 'Panel administrativo')</p>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="admin-user">
                    <span>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->roleLabel() }}</small>
                    </span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="admin-button admin-button-dark" type="submit">Salir</button>
                    </form>
                </div>
            </header>

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
