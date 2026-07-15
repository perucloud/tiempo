@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Operacion administrativa')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Stat cards --}}
    <section class="admin-stats-grid" aria-label="Indicadores principales">
        @foreach ($stats as $stat)
            <article class="admin-card admin-card--{{ $stat['color'] }}">
                <i class="bi {{ $stat['icon'] }} admin-card-icon"></i>
                <p class="admin-card-title">{{ $stat['label'] }}</p>
                <p class="admin-card-value">{{ $stat['value'] }}</p>
                <p class="admin-card-note">{{ $stat['note'] }}</p>
            </article>
        @endforeach
    </section>

    {{-- Main grid: pedidos + accesos --}}
    <div class="admin-dashboard-grid">
        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Pedidos recientes</h2>
                    <p>Ultimos movimientos del sistema</p>
                </div>
                <a class="admin-link" href="{{ route('admin.orders.index') }}">Ver todos &rarr;</a>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td><strong>{{ $order['code'] }}</strong></td>
                                <td>{{ $order['customer'] }}</td>
                                <td><span class="admin-badge admin-badge-yellow">{{ $order['status'] }}</span></td>
                                <td><span class="admin-badge admin-badge-red">{{ $order['payment'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Accesos rapidos</h2>
                    <p>Modulos del sistema</p>
                </div>
            </div>

            <div class="admin-shortcuts">
                @foreach ($adminModules as $module)
                    @continue($module['key'] === 'dashboard')
                    <a class="admin-shortcut" href="{{ $module['url'] }}">
                        <i class="bi {{ $module['icon'] }}"></i>
                        <span>{{ $module['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </article>
    </div>

    {{-- Mobile priority — shown only on small screens --}}
    <section class="admin-panel admin-mobile-priority" aria-label="Operacion movil prioritaria">
        <div class="admin-panel-header">
            <div>
                <h2>Operacion rapida</h2>
                <p>Acciones frecuentes desde celular</p>
            </div>
        </div>

        @foreach ($mobileTasks as $task)
            <article class="admin-mobile-task">
                <span>
                    <strong>{{ $task['label'] }}</strong>
                    <small>{{ $task['note'] }}</small>
                </span>
                <span class="admin-badge">{{ $task['badge'] }}</span>
            </article>
        @endforeach
    </section>
@endsection
