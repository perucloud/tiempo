@extends('layouts.courier')

@section('title', 'Turno — ' . $repartidor->nombreCompleto())

@section('content')

{{-- ── Identidad ── --}}
<div class="courier-id-card">
    <div class="courier-avatar">{{ strtoupper(substr($repartidor->nombres, 0, 1)) }}</div>
    <div class="courier-id-info">
        <strong>{{ $repartidor->nombreCompleto() }}</strong>
        <small>{{ $repartidor->telefono ?? 'Sin teléfono registrado' }}</small>
    </div>
    <span class="courier-estado-badge {{ $repartidor->estado }}">{{ ucfirst($repartidor->estado) }}</span>
</div>

{{-- ── GPS ── --}}
<div class="courier-gps-panel">
    <div class="courier-gps-header">
        <span>Estado GPS</span>
        <span id="gps-posicion-label" class="gps-posicion-label">Sin señal</span>
    </div>
    <div class="courier-gps-body">
        <div class="gps-indicator estado-idle" id="gps-indicator">📍</div>
        <span id="gps-status" class="gps-status-badge gps-status--neutral">Turno no iniciado</span>
        <div class="gps-posicion-wrap">
            <p class="gps-posicion-label">Posición actual</p>
            <p id="gps-posicion">—</p>
        </div>
    </div>
</div>

{{-- ── Acciones de turno ── --}}
<div class="courier-actions">
    <button id="btn-iniciar-turno" class="btn-turno btn-turno--iniciar" type="button"
        @if($repartidor->estado_operativo && $repartidor->estado_operativo !== \App\Models\Repartidor::OP_OFFLINE) style="display:none" @endif>
        🚀 Iniciar turno
    </button>
    <button id="btn-terminar-turno" class="btn-turno btn-turno--terminar
        @if(!$repartidor->estado_operativo || $repartidor->estado_operativo === \App\Models\Repartidor::OP_OFFLINE) hidden @endif"
        type="button">
        🛑 Terminar turno
    </button>
</div>

{{-- ── Pedido activo ── --}}
@if($asignacion)
<div class="courier-pedido-card" id="pedido-card">
    <div class="courier-pedido-header">
        <span class="courier-pedido-codigo">{{ $asignacion->pedido?->codigo }}</span>
        <span id="estado-op-badge" class="courier-op-badge">
            {{ \App\Models\Repartidor::estadoOperativoLabel($repartidor->estado_operativo ?? 'assigned') }}
        </span>
    </div>

    <div class="courier-pedido-info">
        <div class="courier-dest">
            <span class="courier-dest-icon">🏪</span>
            <div>
                <strong>{{ $asignacion->pedido?->negocioAfiliado?->nombre_comercial }}</strong>
                <small>{{ $asignacion->pedido?->negocioAfiliado?->direccion }}</small>
                @if($asignacion->distance_to_business_km)
                <small>{{ $asignacion->distance_to_business_km }} km · ~{{ $asignacion->estimated_time_to_business_min }} min</small>
                @endif
            </div>
        </div>
        <div class="courier-dest">
            <span class="courier-dest-icon">📍</span>
            <div>
                <strong>{{ $asignacion->pedido?->cliente?->nombres }}</strong>
                <small>{{ $asignacion->pedido?->direccion_entrega }}</small>
                @if($asignacion->distance_to_customer_km)
                <small>{{ $asignacion->distance_to_customer_km }} km · ~{{ $asignacion->estimated_time_to_customer_min }} min</small>
                @endif
            </div>
        </div>
    </div>

    {{-- Botones de estado operativo --}}
    <div class="courier-estado-buttons" id="estado-buttons">
        @php $estadoOp = $repartidor->estado_operativo ?? \App\Models\Repartidor::OP_ASSIGNED; @endphp
        @foreach(\App\Models\Repartidor::TRANSICIONES_OPERATIVAS[$estadoOp] ?? [] as $siguiente)
        <button class="btn-estado-op" data-estado="{{ $siguiente }}" type="button">
            {{ match($siguiente) {
                'going_to_business' => '🚗 Voy al negocio',
                'at_business'       => '🏪 Llegué al negocio',
                'picked_up'         => '📦 Recogí el pedido',
                'going_to_customer' => '🛵 En camino al cliente',
                'delivered'         => '✅ Entregué el pedido',
                'available'         => '🟢 Listo para otro pedido',
                default             => ucfirst(str_replace('_', ' ', $siguiente)),
            } }}
        </button>
        @endforeach
    </div>

    {{-- Navegación externa --}}
    <div class="courier-nav-links">
        @php
            $negLat = $asignacion->pedido?->negocioAfiliado?->latitud;
            $negLng = $asignacion->pedido?->negocioAfiliado?->longitud;
            $cliLat = $asignacion->pedido?->latitud_cliente;
            $cliLng = $asignacion->pedido?->longitud_cliente;
        @endphp

        @if($negLat && $negLng)
        <a class="courier-nav-btn courier-nav-gmaps"
           href="https://www.google.com/maps/dir/?api=1&destination={{ $negLat }},{{ $negLng }}&travelmode=driving"
           target="_blank" rel="noopener">
            🗺 Google Maps → Negocio
        </a>
        <a class="courier-nav-btn courier-nav-waze"
           href="https://waze.com/ul?ll={{ $negLat }},{{ $negLng }}&navigate=yes"
           target="_blank" rel="noopener">
            Waze → Negocio
        </a>
        @endif

        @if($cliLat && $cliLng)
        <a class="courier-nav-btn courier-nav-gmaps"
           href="https://www.google.com/maps/dir/?api=1&destination={{ $cliLat }},{{ $cliLng }}&travelmode=driving"
           target="_blank" rel="noopener">
            🗺 Google Maps → Cliente
        </a>
        <a class="courier-nav-btn courier-nav-waze"
           href="https://waze.com/ul?ll={{ $cliLat }},{{ $cliLng }}&navigate=yes"
           target="_blank" rel="noopener">
            Waze → Cliente
        </a>
        @endif
    </div>
</div>
@else
<div class="courier-sin-pedido" id="pedido-card">
    <p>⏳ En espera de pedido…</p>
    <small>El operador te asignará cuando haya un pedido disponible.</small>
</div>
@endif

{{-- ── Instrucciones ── --}}
<details class="courier-instrucciones">
    <summary><strong>¿Cómo funciona?</strong></summary>
    <ul>
        <li>Presiona <strong>Iniciar turno</strong> al comenzar a trabajar.</li>
        <li>El navegador pedirá permiso de GPS — <strong>debes aceptarlo</strong>.</li>
        <li>Tu posición se actualiza automáticamente cada 10 segundos.</li>
        <li>Cuando el operador te asigne un pedido, aparecerá aquí.</li>
        <li>Usa los botones de estado para actualizar tu progreso.</li>
        <li>Presiona <strong>Terminar turno</strong> al finalizar.</li>
    </ul>
</details>

@endsection

@push('courier_scripts')
<script>
    window.TIEMPO_REPARTIDOR_ID = {{ $repartidor->id }};
    window.TIEMPO_ESTADO_OP     = '{{ $repartidor->estado_operativo ?? "offline" }}';
    window.TIEMPO_ESTADO_URL    = '{{ route("courier.estado.update", $repartidor) }}';
</script>
<script src="{{ asset('js/tracking-repartidor.js') }}" defer></script>
<script>
(function () {
    const ESTADO_URL = window.TIEMPO_ESTADO_URL;
    const REPARTIDOR_ID = window.TIEMPO_REPARTIDOR_ID;

    /* ── Sincronizar indicador GPS con el badge ── */
    const indicator = document.getElementById('gps-indicator');
    const posLabel  = document.getElementById('gps-posicion-label');
    const badge     = document.getElementById('gps-status');

    if (indicator && badge) {
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
    }

    /* ── Iniciar turno: cambia estado_operativo a "available" ── */
    const btnIniciar  = document.getElementById('btn-iniciar-turno');
    const btnTerminar = document.getElementById('btn-terminar-turno');

    function postEstado(estado) {
        return fetch(ESTADO_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ estado_operativo: estado }),
        }).then(r => r.json());
    }

    if (btnIniciar) {
        btnIniciar.addEventListener('click', async function () {
            await postEstado('available');
            /* El tracking GPS ya lo inicia tracking-repartidor.js via el botón */
        });
    }

    if (btnTerminar) {
        btnTerminar.addEventListener('click', async function () {
            await postEstado('offline');
        });
    }

    /* ── Botones de estado operativo (flujo del pedido) ── */
    document.querySelectorAll('.btn-estado-op').forEach(btn => {
        btn.addEventListener('click', async function () {
            btn.disabled = true;
            const nuevoEstado = btn.dataset.estado;

            try {
                const json = await postEstado(nuevoEstado);

                /* Actualizar badge */
                const badgeEl = document.getElementById('estado-op-badge');
                if (badgeEl && json.label) badgeEl.textContent = json.label;

                /* Actualizar botones con las nuevas transiciones */
                const container = document.getElementById('estado-buttons');
                if (container && json.siguientes) {
                    const labels = {
                        going_to_business : '🚗 Voy al negocio',
                        at_business       : '🏪 Llegué al negocio',
                        picked_up         : '📦 Recogí el pedido',
                        going_to_customer : '🛵 En camino al cliente',
                        delivered         : '✅ Entregué el pedido',
                        available         : '🟢 Listo para otro pedido',
                    };
                    container.innerHTML = json.siguientes.map(s =>
                        `<button class="btn-estado-op" data-estado="${s}" type="button">${labels[s] ?? s}</button>`
                    ).join('');
                    /* Re-bind */
                    container.querySelectorAll('.btn-estado-op').forEach(b => {
                        b.addEventListener('click', arguments.callee.parentFunction);
                    });
                }

                /* Si se entregó, recargar para limpiar la tarjeta de pedido */
                if (nuevoEstado === 'delivered') {
                    setTimeout(() => window.location.reload(), 1500);
                }
            } catch (e) {
                btn.disabled = false;
            }
        });
    });
})();
</script>
@endpush
