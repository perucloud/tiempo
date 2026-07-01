@extends('layouts.admin')

@section('title', 'Reportes')
@section('eyebrow', 'Inteligencia operativa')
@section('page-title', 'Reportes administrativos')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Filtros</h2>
                <p>Reportes globales de TIEMPO. Los negocios afiliados no tienen acceso a esta informacion.</p>
            </div>
        </div>

        <form class="admin-filter-bar admin-filter-bar-compact" method="GET" action="{{ route('admin.reports.index') }}">
            <label class="admin-field">
                <span>Desde</span>
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}">
                @error('date_from') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Hasta</span>
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}">
                @error('date_to') <small>{{ $message }}</small> @enderror
            </label>

            <div class="admin-filter-actions">
                <button class="admin-button admin-button-dark" type="submit">Filtrar</button>
                <a class="admin-button" href="{{ route('admin.reports.index') }}">Hoy / mes actual</a>
            </div>
        </form>
    </section>

    <section class="admin-grid admin-grid-4" aria-label="Resumen de reportes">
        <article class="admin-card">
            <p class="admin-card-title">Ventas entregadas</p>
            <p class="admin-card-value">S/ {{ number_format($summary['sales_total'], 2) }}</p>
            <p class="admin-card-note">Pedidos finalizados</p>
        </article>

        <article class="admin-card">
            <p class="admin-card-title">Pagos aprobados</p>
            <p class="admin-card-value">S/ {{ number_format($summary['payments_total'], 2) }}</p>
            <p class="admin-card-note">{{ $summary['pending_payments'] }} pagos pendientes</p>
        </article>

        <article class="admin-card">
            <p class="admin-card-title">Pedidos</p>
            <p class="admin-card-value">{{ $summary['orders'] }}</p>
            <p class="admin-card-note">{{ $summary['delivered_orders'] }} entregados</p>
        </article>

        <article class="admin-card">
            <p class="admin-card-title">Operacion</p>
            <p class="admin-card-value">{{ $summary['available_couriers'] }}</p>
            <p class="admin-card-note">{{ $summary['active_businesses'] }} negocios activos</p>
        </article>
    </section>

    <section class="admin-grid admin-grid-3">
        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Pedidos por estado</h2>
                    <p>Distribucion del periodo seleccionado.</p>
                </div>
            </div>
            <div class="admin-module-list">
                @foreach ($estadoOptions as $value => $label)
                    <div class="admin-module-item">
                        <span>
                            <strong>{{ $label }}</strong>
                            <small>{{ $value }}</small>
                        </span>
                        <span class="admin-badge">{{ $ordersByStatus[$value] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Pagos por metodo</h2>
                    <p>Conteo y monto registrado.</p>
                </div>
            </div>
            <div class="admin-module-list">
                @forelse ($paymentsByMethod as $payment)
                    <div class="admin-module-item">
                        <span>
                            <strong>{{ $metodoOptions[$payment->metodo] ?? ucfirst($payment->metodo) }}</strong>
                            <small>{{ $payment->total }} operaciones</small>
                        </span>
                        <span>S/ {{ number_format((float) $payment->amount, 2) }}</span>
                    </div>
                @empty
                    <div class="admin-module-item">
                        <span>Sin pagos en el periodo.</span>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Repartidores</h2>
                    <p>Entregas completadas por repartidor.</p>
                </div>
            </div>
            <div class="admin-module-list">
                @forelse ($courierPerformance as $row)
                    <div class="admin-module-item">
                        <span>
                            <strong>{{ $row->repartidor?->nombreCompleto() ?? 'Sin repartidor' }}</strong>
                            <small>Pedidos entregados</small>
                        </span>
                        <span class="admin-badge admin-badge-green">{{ $row->delivered_count }}</span>
                    </div>
                @empty
                    <div class="admin-module-item">
                        <span>Sin entregas en el periodo.</span>
                    </div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Top negocios afiliados</h2>
                <p>Ventas entregadas dentro del periodo.</p>
            </div>
        </div>

        <div class="admin-table-wrap admin-desktop-table">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Negocio</th>
                        <th>Pedidos entregados</th>
                        <th>Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topBusinesses as $business)
                        <tr>
                            <td>{{ $business->negocioAfiliado?->nombre_comercial ?? 'Negocio no disponible' }}</td>
                            <td>{{ $business->orders_count }}</td>
                            <td>S/ {{ number_format((float) $business->sales_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No hay ventas entregadas en el periodo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
