<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | TIEMPO Delivery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
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
                        <i class="bi {{ $module['icon'] }} admin-nav-icon"></i>
                        <span>{{ $module['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="admin-sidebar-footer">
                <div class="admin-sidebar-user">
                    <span class="admin-sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->roleLabel() }}</small>
                    </span>
                </div>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div>
                    <p class="admin-eyebrow">@yield('eyebrow', 'Panel administrativo')</p>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="admin-user">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="admin-button admin-button-logout" type="submit">
                            <i class="bi bi-box-arrow-right"></i>
                            Salir
                        </button>
                    </form>
                </div>
            </header>

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        /* Floating submenu infrastructure — ready for use when nav groups are added */
        document.querySelectorAll('.admin-nav-group-trigger').forEach(trigger => {
            trigger.addEventListener('click', e => {
                e.preventDefault();
                const group = trigger.closest('.admin-nav-group');
                const submenu = group?.querySelector('.admin-submenu');
                if (!submenu) return;
                const rect = trigger.getBoundingClientRect();
                submenu.style.top = rect.top + 'px';
                group.classList.toggle('is-open');
            });
        });
        document.addEventListener('click', e => {
            if (!e.target.closest('.admin-nav-group')) {
                document.querySelectorAll('.admin-nav-group.is-open').forEach(g => g.classList.remove('is-open'));
            }
        });
    </script>
</body>
</html>
