@extends('layouts.app-mobile')

@section('title', $pedido->codigo . ' — TIEMPO Delivery')
@section('description', 'Sigue tu pedido en tiempo real.')

@php
/* ── Orden de estados para la timeline ── */
$TIMELINE = [
    'pendiente'        => ['label' => 'Pedido creado',        'icon' => '📋'],
    'pago_en_revision' => ['label' => 'Pago en revisión',     'icon' => '⏳'],
    'confirmado'       => ['label' => 'Confirmado',           'icon' => '✅'],
    'preparando'       => ['label' => 'En preparación',       'icon' => '🍳'],
    'listo'            => ['label' => 'Listo para recojo',    'icon' => '📦'],
    'asignado'         => ['label' => 'Repartidor asignado',  'icon' => '🛵'],
    'en_camino'        => ['label' => 'En camino',            'icon' => '🚗'],
    'entregado'        => ['label' => '¡Entregado!',          'icon' => '🎉'],
];

$ORDEN = array_keys($TIMELINE);
$estadoActual = $pedido->estado;
$cancelado    = $estadoActual === 'cancelado';
$posActual    = array_search($estadoActual, $ORDEN);

/* Mostrar mapa si repartidor está en tránsito con GPS */
$rep = $pedido->repartidor;
$mostrarMapa = $rep && $rep->latitud_actual
    && in_array($estadoActual, ['asignado', 'en_camino'], true);

/* Mostrar formulario de pago solo si el pedido está pendiente de pago */
$mostrarPago = in_array($estadoActual, ['pendiente'], true)
    && $pedido->pagos->isEmpty();
@endphp

@section('content')

{{-- ── Header ── --}}
<div class="tracking-header">
    <a href="{{ route('app.home') }}" class="tracking-back" aria-label="Volver">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div class="tracking-header-info">
        <span class="tracking-codigo">{{ $pedido->codigo }}</span>
        @if($cancelado)
            <span class="tracking-estado-badge cancelled">Cancelado</span>
        @else
            <span class="tracking-estado-badge" id="estado-badge">
                {{ \App\Models\Pedido::ESTADOS_CLIENTE[$estadoActual] ?? $estadoActual }}
            </span>
        @endif
    </div>
    <div class="tracking-refresh" id="refresh-indicator" title="Actualizando automáticamente">⟳</div>
</div>

{{-- Alertas --}}
@if(session('pay_status'))
    <div class="tracking-alert tracking-alert-ok">{{ session('pay_status') }}</div>
@endif
@if(session('pay_error'))
    <div class="tracking-alert tracking-alert-err">{{ session('pay_error') }}</div>
@endif

{{-- ── Timeline ── --}}
@if(!$cancelado)
<div class="tracking-timeline">
    @foreach($TIMELINE as $key => $step)
        @php
            $pos  = array_search($key, $ORDEN);
            $done = $pos < $posActual;
            $curr = $key === $estadoActual;
        @endphp
        <div class="timeline-step {{ $done ? 'done' : ($curr ? 'current' : 'pending') }}">
            <div class="timeline-dot">{{ $done ? '✓' : $step['icon'] }}</div>
            <div class="timeline-label">{{ $step['label'] }}</div>
            @if($curr)
                @php
                    $ts = $pedido->estados->where('estado_nuevo', $key)->sortByDesc('created_at')->first();
                @endphp
                @if($ts)
                    <div class="timeline-time">{{ $ts->created_at->format('H:i') }}</div>
                @endif
            @endif
        </div>
    @endforeach
</div>
@else
<div class="tracking-alert tracking-alert-err">Este pedido fue cancelado.</div>
@endif

{{-- ── Mapa del repartidor ── --}}
@if($mostrarMapa)
<div class="tracking-section">
    <h3 class="tracking-section-title">🛵 Repartidor en camino</h3>
    <div class="tracking-courier-info">
        <span>{{ $rep->nombreCompleto() }}</span>
        @if($rep->vehiculo_tipo)
            <span>{{ ucfirst($rep->vehiculo_tipo) }} {{ $rep->vehiculo_placa }}</span>
        @endif
        @if($rep->telefono)
            <a href="tel:{{ $rep->telefono }}" class="tracking-call-btn">📞 Llamar</a>
        @endif
    </div>
    <div id="courierMap" class="tracking-map"></div>
</div>
@endif

{{-- ── Detalles del pedido ── --}}
<div class="tracking-section">
    <h3 class="tracking-section-title">🏪 {{ $pedido->negocioAfiliado?->nombre_comercial }}</h3>
    <div class="tracking-items">
        @foreach($pedido->detalles as $detalle)
            <div class="tracking-item">
                <span class="tracking-item-name">{{ $detalle->producto_nombre }} ×{{ $detalle->cantidad }}</span>
                <span class="tracking-item-price">S/ {{ number_format($detalle->subtotal, 2) }}</span>
            </div>
        @endforeach
    </div>
    <div class="tracking-totals">
        <div class="tracking-total-row">
            <span>Subtotal</span>
            <span>S/ {{ number_format($pedido->subtotal, 2) }}</span>
        </div>
        <div class="tracking-total-row">
            <span>Delivery</span>
            <span>S/ {{ number_format($pedido->costo_delivery, 2) }}</span>
        </div>
        <div class="tracking-total-row tracking-total-final">
            <span>Total</span>
            <span>S/ {{ number_format($pedido->total, 2) }}</span>
        </div>
    </div>
    @if($pedido->direccion_entrega)
        <p class="tracking-address">📍 {{ $pedido->direccion_entrega }}</p>
    @endif
</div>

{{-- ── Pago ── --}}
@if($mostrarPago)
<div class="tracking-section">
    <h3 class="tracking-section-title">💳 Registra tu pago</h3>
    <p class="tracking-pay-hint">Tu pedido está esperando confirmación de pago. Transfiere <strong>S/ {{ number_format($pedido->total, 2) }}</strong> por Yape o Plin y sube el voucher.</p>

    <form class="tracking-pay-form" method="POST" action="{{ route('app.payments.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="codigo" value="{{ $pedido->codigo }}">

        <label for="pay-method">Método</label>
        <select id="pay-method" name="metodo" required>
            @foreach(\App\Models\Pago::metodoOptions() as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>

        <label for="pay-operation">Código de operación</label>
        <input id="pay-operation" name="codigo_operacion" type="text"
               placeholder="Número de referencia (opcional)">

        <label for="pay-voucher" class="voucher-label">
            <span>📎 Subir captura / voucher</span>
            <input id="pay-voucher" name="voucher" type="file" accept="image/*" class="voucher-input">
            <span class="voucher-hint" id="voucher-filename">JPG, PNG o WEBP · máx 5 MB</span>
        </label>

        <button class="tracking-pay-btn" type="submit">Enviar pago</button>
    </form>
</div>
@endif

{{-- ── Pago registrado ── --}}
@if($pedido->pagos->isNotEmpty())
    @php $pago = $pedido->pagos->first(); @endphp
    <div class="tracking-section">
        <h3 class="tracking-section-title">💳 Pago registrado</h3>
        <div class="tracking-pay-status">
            <span class="pay-badge pay-badge-{{ $pago->estado }}">{{ $pago->estadoLabel() }}</span>
            <span>{{ strtoupper($pago->metodo) }} · S/ {{ number_format($pago->monto, 2) }}</span>
        </div>
    </div>
@endif

{{-- ── Notas ── --}}
@if($pedido->notas)
    <div class="tracking-section tracking-notes">
        <strong>Notas:</strong> {{ $pedido->notas }}
    </div>
@endif

@endsection

@push('app_scripts')
@if($mostrarMapa)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLs=" crossorigin=""></script>
<script>
(function() {
    const lat = {{ $rep->latitud_actual }};
    const lng = {{ $rep->longitud_actual }};
    const map = L.map('courierMap').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19,
    }).addTo(map);
    const marker = L.circleMarker([lat, lng], {
        radius: 10, color: '#0f766e', fillColor: '#14b8a6', fillOpacity: 1, weight: 3,
    }).bindPopup('🛵 {{ addslashes($rep->nombreCompleto()) }}').addTo(map);
    marker.openPopup();
})();
</script>
@endif

<script>
(function() {
    /* ── Polling de estado cada 15s ── */
    const ESTADO_URL = '{{ route("app.orders.estado", $pedido->codigo) }}';
    const TERMINAL   = ['entregado', 'cancelado'];
    const badge      = document.getElementById('estado-badge');
    const indicator  = document.getElementById('refresh-indicator');
    let estadoActual = '{{ $estadoActual }}';

    function poll() {
        if (TERMINAL.includes(estadoActual)) return;

        fetch(ESTADO_URL, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.estado !== estadoActual) {
                    estadoActual = data.estado;
                    if (badge) badge.textContent = data.label;
                    /* Recargar página para actualizar timeline completa */
                    window.location.reload();
                }
                indicator.style.opacity = '1';
                setTimeout(() => indicator.style.opacity = '.3', 500);
            })
            .catch(() => {});
    }

    setInterval(poll, 15000);
    setTimeout(() => indicator.style.opacity = '.3', 1000);

    /* ── Mostrar nombre del archivo seleccionado ── */
    const fileInput = document.getElementById('pay-voucher');
    if (fileInput) {
        fileInput.addEventListener('change', () => {
            const hint = document.getElementById('voucher-filename');
            if (hint) hint.textContent = fileInput.files[0]?.name ?? 'Sin archivo';
        });
    }
})();
</script>
@endpush
