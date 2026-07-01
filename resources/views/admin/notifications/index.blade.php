@extends('layouts.admin')

@section('title', 'Notificaciones')
@section('eyebrow', 'Comunicacion interna')
@section('page-title', 'Notificaciones')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Eventos del sistema</h2>
                <p>Base interna para notificaciones de pagos, estados de pedido y asignaciones. No contiene datos sensibles.</p>
            </div>
        </div>

        <form class="admin-filter-bar admin-filter-bar-compact" method="GET" action="{{ route('admin.notifications.index') }}">
            <label class="admin-field">
                <span>Destinatario</span>
                <select name="destinatario_tipo">
                    <option value="">Todos</option>
                    @foreach ($recipientOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['destinatario_tipo'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field">
                <span>Tipo</span>
                <select name="tipo">
                    <option value="">Todos</option>
                    @foreach ($typeOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['tipo'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="admin-filter-actions">
                <button class="admin-button admin-button-dark" type="submit">Filtrar</button>
                <a class="admin-button" href="{{ route('admin.notifications.index') }}">Limpiar</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Destinatario</th>
                        <th>Pedido</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notifications as $notification)
                        <tr>
                            <td>
                                <strong>{{ $notification->titulo }}</strong>
                                <small>{{ $typeOptions[$notification->tipo] ?? $notification->tipo }}</small>
                            </td>
                            <td>{{ $recipientOptions[$notification->destinatario_tipo] ?? $notification->destinatario_tipo }}</td>
                            <td>{{ $notification->pedido?->codigo ?? 'Sin pedido' }}</td>
                            <td>{{ $notification->mensaje }}</td>
                            <td>{{ $notification->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No hay notificaciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $notifications->links() }}
        </div>
    </section>
@endsection
