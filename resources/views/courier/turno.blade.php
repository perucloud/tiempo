@extends('layouts.courier')

@section('title', 'Turno — ' . $repartidor->nombre)

@section('content')

    {{-- Identidad del repartidor --}}
    <div class="courier-id-card">
        <div class="courier-avatar">{{ strtoupper(substr($repartidor->nombre, 0, 1)) }}</div>
        <div class="courier-id-info">
            <strong>{{ $repartidor->nombre }}</strong>
            <small>{{ $repartidor->telefono ?? 'Sin teléfono registrado' }}</small>
        </div>
        <span class="courier-estado-badge {{ $repartidor->estado }}">{{ ucfirst($repartidor->estado) }}</span>
    </div>

    {{-- Panel GPS --}}
    <div class="courier-gps-panel">
        <div class="courier-gps-header">
            <span>Estado GPS</span>
            <span id="gps-posicion-label" class="gps-posicion-label">Sin señal</span>
        </div>
        <div class="courier-gps-body">
            <div class="gps-indicator estado-idle" id="gps-indicator">
                📍
            </div>
            <span id="gps-status" class="gps-status-badge gps-status--neutral">
                Turno no iniciado
            </span>
            <div class="gps-posicion-wrap">
                <p class="gps-posicion-label">Posición actual</p>
                <p id="gps-posicion">—</p>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="courier-actions">
        <button id="btn-iniciar-turno" class="btn-turno btn-turno--iniciar" type="button">
            🚀 Iniciar turno
        </button>
        <button id="btn-terminar-turno" class="btn-turno btn-turno--terminar hidden" type="button">
            🛑 Terminar turno
        </button>
    </div>

    {{-- Instrucciones --}}
    <div class="courier-instrucciones">
        <p><strong>¿Cómo funciona?</strong></p>
        <ul>
            <li>Presiona <strong>Iniciar turno</strong> al comenzar a trabajar.</li>
            <li>El navegador pedirá permiso de GPS — <strong>debes aceptarlo</strong>.</li>
            <li>Tu posición se actualiza automáticamente cada 10 segundos.</li>
            <li>Mantén esta pestaña abierta durante todo el turno.</li>
            <li>Presiona <strong>Terminar turno</strong> al finalizar.</li>
        </ul>
    </div>

@endsection

@push('courier_scripts')
<script>
    window.TIEMPO_REPARTIDOR_ID = {{ $repartidor->id }};
</script>
<script src="{{ asset('js/tracking-repartidor.js') }}" defer></script>
<script>
    /* Sincroniza el indicador circular con los estados del badge */
    (function () {
        const indicator = document.getElementById('gps-indicator');
        const posLabel  = document.getElementById('gps-posicion-label');
        const badge     = document.getElementById('gps-status');

        if (!indicator || !badge) return;

        const observer = new MutationObserver(function () {
            const cls = badge.className;
            indicator.className = 'gps-indicator ' + (
                cls.includes('active')  ? 'estado-active'  :
                cls.includes('loading') ? 'estado-loading' :
                cls.includes('error')   ? 'estado-error'   :
                cls.includes('warn')    ? 'estado-loading' : 'estado-idle'
            );

            const pos = document.getElementById('gps-posicion');
            if (posLabel) posLabel.textContent = pos && pos.textContent !== '—' ? pos.textContent : 'Sin señal';
        });

        observer.observe(badge, { attributes: true, childList: true, subtree: true });
    })();
</script>
@endpush
