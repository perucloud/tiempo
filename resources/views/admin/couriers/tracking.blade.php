@extends('layouts.admin')

@section('title', 'Tracking en vivo')
@section('eyebrow', 'Operacion de delivery')
@section('page-title', 'Tracking en vivo')

@section('content')
    <div class="tracking-shell">
        {{-- Panel lateral: lista de repartidores --}}
        <aside class="tracking-sidebar">
            <div class="tracking-sidebar-header">
                <h2><i class="bi bi-broadcast"></i> Repartidores activos</h2>
                <span class="admin-badge" id="tracking-count">{{ $couriers->count() }}</span>
            </div>

            <div class="tracking-courier-list" id="tracking-courier-list">
                @forelse ($couriers as $courier)
                    <article class="tracking-courier-card {{ $courier->tieneGpsActivo() ? 'is-live' : '' }}" data-id="{{ $courier->id }}">
                        <div class="tracking-courier-avatar">{{ strtoupper(substr($courier->nombres, 0, 1)) }}</div>
                        <div class="tracking-courier-info">
                            <strong>{{ $courier->nombreCompleto() }}</strong>
                            <span class="admin-badge {{ $courier->estado === 'disponible' ? 'admin-badge-green' : 'admin-badge-yellow' }}">
                                {{ $courier->estado === 'disponible' ? 'Disponible' : 'En ruta' }}
                            </span>
                            <small class="tracking-gps-status {{ $courier->tieneGpsActivo() ? 'gps-on' : 'gps-off' }}">
                                <i class="bi {{ $courier->tieneGpsActivo() ? 'bi-reception-4' : 'bi-wifi-off' }}"></i>
                                {{ $courier->tieneGpsActivo() ? 'GPS activo' : ($courier->ubicacion_actualizada_at ? 'Sin señal — ' . $courier->ubicacion_actualizada_at->diffForHumans() : 'Sin GPS registrado') }}
                            </small>
                        </div>
                    </article>
                @empty
                    <div class="tracking-empty">
                        <i class="bi bi-bicycle"></i>
                        <p>No hay repartidores activos en este momento.</p>
                    </div>
                @endforelse
            </div>

            <div class="tracking-sidebar-footer">
                <small id="tracking-last-update">Actualizando...</small>
                <button class="admin-link" id="tracking-refresh-btn" type="button">
                    <i class="bi bi-arrow-clockwise"></i> Refrescar
                </button>
            </div>
        </aside>

        {{-- Mapa principal --}}
        <div class="tracking-map-container">
            <div id="mapa-tracking"></div>

            <div class="tracking-map-legend">
                <span><span class="legend-dot dot-green"></span> Disponible</span>
                <span><span class="legend-dot dot-orange"></span> En ruta</span>
                <span><span class="legend-dot dot-gray"></span> Sin señal</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const UBICACIONES_URL = '{{ route('admin.couriers.ubicaciones') }}';
</script>
<script src="{{ asset('js/mapa-admin.js') }}"></script>
@endpush
