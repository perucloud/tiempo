@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Operacion administrativa')
@section('page-title', 'Dashboard administrativo')

@section('content')
    <section class="admin-grid admin-grid-4" aria-label="Indicadores principales">
        @foreach ($stats as $stat)
            <article class="admin-card">
                <p class="admin-card-title">{{ $stat['label'] }}</p>
                <p class="admin-card-value">{{ $stat['value'] }}</p>
                <p class="admin-card-note">{{ $stat['note'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="admin-panel admin-mobile-priority" aria-label="Operacion movil prioritaria">
        <div class="admin-panel-header">
            <div>
                <h2>Operacion rapida movil</h2>
                <p>Acciones frecuentes para duenos y operadores desde celular.</p>
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

    <section class="admin-grid admin-grid-3">
        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Modulos administrativos</h2>
                    <p>Mapa inicial del sistema TIEMPO.</p>
                </div>
            </div>

            <div class="admin-module-list">
                @foreach ($adminModules as $module)
                    @continue($module['label'] === 'Dashboard')
                    <div class="admin-module-item">
                        <span>
                            <strong>{{ $module['label'] }}</strong>
                            <small>Disponible en su fase correspondiente</small>
                        </span>
                        <span class="admin-badge admin-badge-yellow">Planificado</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Pedidos recientes</h2>
                    <p>Vista desktop con tabla operativa base.</p>
                </div>
            </div>

            <div class="admin-table-wrap admin-desktop-table">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Cliente</th>
                            <th>Pedido</th>
                            <th>Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td>{{ $order['code'] }}</td>
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
                    <h2>Reglas de acceso</h2>
                    <p>Separacion inicial por rol.</p>
                </div>
            </div>

            <div class="admin-module-list">
                <div class="admin-module-item">
                    <span>
                        <strong>TIEMPO opera pedidos</strong>
                        <small>Operadores gestionan pagos, estados y repartidores.</small>
                    </span>
                    <span class="admin-badge admin-badge-green">Activo</span>
                </div>
                <div class="admin-module-item">
                    <span>
                        <strong>Negocio afiliado limitado</strong>
                        <small>Solo perfil, carta, productos y horarios propios.</small>
                    </span>
                    <span class="admin-badge">Rol</span>
                </div>
                <div class="admin-module-item">
                    <span>
                        <strong>Cliente fuera de admin</strong>
                        <small>La experiencia del cliente vive en /app.</small>
                    </span>
                    <span class="admin-badge admin-badge-red">Bloqueado</span>
                </div>
            </div>
        </article>
    </section>
@endsection
