@extends('layouts.admin')

@section('title', 'Asignar repartidor — ' . $order->codigo)
@section('eyebrow', 'Pedidos')
@section('page-title', 'Asignar repartidor')

@section('content')
<div class="asign-layout">

    {{-- ═══ PANEL IZQUIERDO ═══ --}}
    <aside class="asign-sidebar">

        {{-- Pedido info --}}
        <div class="asign-card">
            <div class="asign-card-header">
                <span class="asign-codigo">{{ $order->codigo }}</span>
                <span class="admin-badge admin-badge-{{ $order->estado }}">{{ $order->estadoLabel() }}</span>
            </div>
            <dl class="asign-meta">
                <dt>Negocio</dt>
                <dd>{{ $order->negocioAfiliado?->nombre_comercial ?? '—' }}</dd>
                <dt>Cliente</dt>
                <dd>{{ $order->cliente?->nombres ?? '—' }}</dd>
                <dt>Dirección entrega</dt>
                <dd>{{ $order->direccion_entrega ?? '—' }}</dd>
                <dt>Total</dt>
                <dd>S/ {{ $order->total }}</dd>
            </dl>
        </div>

        @if($order->asignacionActiva)
        <div class="asign-alert asign-alert-warn">
            <strong>Ya tiene repartidor asignado:</strong>
            {{ $order->repartidor?->nombreCompleto() }}.
            Para reasignar, primero cancela la asignación actual desde el pedido.
        </div>
        @endif

        {{-- Candidatos --}}
        <div class="asign-candidates-header">
            <h3>Candidatos cercanos</h3>
            <button class="admin-button admin-button-sm" id="btnRefresh" type="button">↺ Actualizar</button>
        </div>

        <div id="candidatesList">
            <div class="asign-loading">Calculando candidatos...</div>
        </div>

        <a class="admin-button asign-back-btn" href="{{ route('admin.orders.edit', $order) }}">← Volver al pedido</a>
    </aside>

    {{-- ═══ MAPA ═══ --}}
    <div class="asign-map-wrap">
        <div id="asignMap" class="asign-map"></div>
        <div class="asign-map-legend">
            <span class="legend-item"><span class="legend-dot legend-blue"></span> Negocio</span>
            <span class="legend-item"><span class="legend-dot legend-red"></span> Cliente</span>
            <span class="legend-item"><span class="legend-dot legend-green"></span> Repartidor disponible</span>
        </div>
    </div>
</div>

{{-- Modal confirmación --}}
<div class="asign-modal-overlay hidden" id="confirmModal">
    <div class="asign-modal">
        <h4>¿Confirmar asignación?</h4>
        <p id="confirmText"></p>
        <div class="asign-modal-actions">
            <button class="admin-button admin-button-dark" id="btnConfirmAssign">Sí, asignar</button>
            <button class="admin-button" id="btnCancelModal">Cancelar</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLs=" crossorigin=""></script>

<script>
(function () {
    const CANDIDATOS_URL = '{{ route("admin.orders.candidatos", $order) }}';
    const ASIGNAR_URL    = '{{ route("admin.orders.asignar.store", $order) }}';
    const CSRF_TOKEN     = '{{ csrf_token() }}';

    /* ── Mapa ── */
    const map = L.map('asignMap').setView([-11.2534, -74.6362], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors', maxZoom: 19,
    }).addTo(map);

    let negocioMarker = null;
    let clienteMarker = null;
    const driverMarkers = {};
    let selectedRoute = null;
    let selectedDriverId = null;

    function makeIcon(color, emoji) {
        return L.divIcon({
            className: '',
            html: `<div class="map-marker-pin" style="--mc:${color}">${emoji}</div>`,
            iconSize: [36, 36], iconAnchor: [18, 36], popupAnchor: [0, -36],
        });
    }

    /* ── Cargar candidatos ── */
    async function loadCandidates() {
        document.getElementById('candidatesList').innerHTML = '<div class="asign-loading">Calculando...</div>';

        try {
            const res  = await fetch(CANDIDATOS_URL, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            renderCandidates(json);
        } catch (e) {
            document.getElementById('candidatesList').innerHTML = '<div class="asign-error">Error al cargar candidatos.</div>';
        }
    }

    function renderCandidates(json) {
        /* Poner negocio en mapa */
        if (json.negocio?.latitud) {
            if (negocioMarker) map.removeLayer(negocioMarker);
            negocioMarker = L.marker([json.negocio.latitud, json.negocio.longitud], { icon: makeIcon('#2563eb', '🏪') })
                .bindPopup(`<strong>${json.negocio.nombre}</strong>`)
                .addTo(map);
        }

        /* Poner cliente en mapa */
        if (json.cliente?.latitud) {
            if (clienteMarker) map.removeLayer(clienteMarker);
            clienteMarker = L.marker([json.cliente.latitud, json.cliente.longitud], { icon: makeIcon('#dc2626', '📍') })
                .bindPopup(`<strong>Cliente:</strong> ${json.cliente.nombre}`)
                .addTo(map);
        }

        /* Limpiar marcadores de drivers anteriores */
        Object.values(driverMarkers).forEach(m => map.removeLayer(m));
        Object.keys(driverMarkers).forEach(k => delete driverMarkers[k]);

        const data = json.data ?? [];

        if (data.length === 0) {
            document.getElementById('candidatesList').innerHTML = `
                <div class="asign-empty">
                    <p>No hay repartidores disponibles con GPS activo.</p>
                    <small>Los repartidores deben tener el turno iniciado y GPS activo.</small>
                </div>`;
            return;
        }

        /* Renderizar lista */
        const html = data.map((d, i) => `
            <div class="asign-candidate" data-id="${d.repartidor_id}" data-idx="${i}">
                <div class="asign-candidate-header">
                    <div class="asign-rank">#${i + 1}</div>
                    <div class="asign-candidate-name">
                        <strong>${d.nombre}</strong>
                        <small>${d.vehiculo_tipo ?? ''} ${d.vehiculo_placa ?? ''}</small>
                    </div>
                    <div class="asign-candidate-dist">
                        <span class="dist-km">${d.distance_to_business_km} km</span>
                        <span class="dist-min">~${d.estimated_minutes_to_business} min</span>
                    </div>
                </div>
                <div class="asign-candidate-actions">
                    <button class="admin-button admin-button-sm btn-ver-ruta"
                            data-id="${d.repartidor_id}" type="button">Ver ruta</button>
                    <button class="admin-button admin-button-sm admin-button-dark btn-asignar"
                            data-id="${d.repartidor_id}" data-nombre="${d.nombre}" type="button">Asignar</button>
                </div>
            </div>`).join('');

        document.getElementById('candidatesList').innerHTML = html;

        /* Poner drivers en mapa */
        data.forEach(d => {
            if (!d.latitud) return;
            const m = L.marker([d.latitud, d.longitud], { icon: makeIcon('#16a34a', '🛵') })
                .bindPopup(`<strong>${d.nombre}</strong><br>${d.distance_to_business_km} km · ~${d.estimated_minutes_to_business} min`)
                .addTo(map);
            driverMarkers[d.repartidor_id] = { marker: m, route: d.route_geometry };
        });

        /* Ajustar bounds */
        const allPoints = [];
        if (negocioMarker) allPoints.push(negocioMarker.getLatLng());
        if (clienteMarker) allPoints.push(clienteMarker.getLatLng());
        Object.values(driverMarkers).forEach(({ marker }) => allPoints.push(marker.getLatLng()));
        if (allPoints.length) map.fitBounds(L.latLngBounds(allPoints), { padding: [30, 30] });

        /* Eventos de botones */
        document.querySelectorAll('.btn-ver-ruta').forEach(btn => {
            btn.addEventListener('click', () => showRoute(parseInt(btn.dataset.id), data));
        });
        document.querySelectorAll('.btn-asignar').forEach(btn => {
            btn.addEventListener('click', () => openConfirm(parseInt(btn.dataset.id), btn.dataset.nombre));
        });
    }

    function showRoute(driverId, data) {
        if (selectedRoute) { map.removeLayer(selectedRoute); selectedRoute = null; }
        selectedDriverId = driverId;

        const entry = driverMarkers[driverId];
        if (!entry || !entry.route || entry.route.length < 2) return;

        const coords = entry.route.map(([lng, lat]) => [lat, lng]);
        selectedRoute = L.polyline(coords, { color: '#2563eb', weight: 4 }).addTo(map);
        map.fitBounds(selectedRoute.getBounds(), { padding: [20, 20] });

        /* Resaltar candidato */
        document.querySelectorAll('.asign-candidate').forEach(el => el.classList.remove('selected'));
        document.querySelector(`.asign-candidate[data-id="${driverId}"]`)?.classList.add('selected');
    }

    /* ── Modal de confirmación ── */
    function openConfirm(driverId, nombre) {
        selectedDriverId = driverId;
        document.getElementById('confirmText').textContent =
            `Asignar ${nombre} al pedido {{ $order->codigo }}.`;
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    document.getElementById('btnCancelModal').addEventListener('click', () => {
        document.getElementById('confirmModal').classList.add('hidden');
    });

    document.getElementById('btnConfirmAssign').addEventListener('click', async () => {
        if (!selectedDriverId) return;
        document.getElementById('btnConfirmAssign').disabled = true;
        document.getElementById('btnConfirmAssign').textContent = 'Asignando...';

        try {
            const res = await fetch(ASIGNAR_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({ repartidor_id: selectedDriverId }),
            });
            const json = await res.json();

            if (res.ok) {
                window.location.href = json.redirect;
            } else {
                alert(json.message ?? 'Error al asignar.');
                document.getElementById('confirmModal').classList.add('hidden');
                document.getElementById('btnConfirmAssign').disabled = false;
                document.getElementById('btnConfirmAssign').textContent = 'Sí, asignar';
                loadCandidates();
            }
        } catch (e) {
            alert('Error de conexión.');
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('btnConfirmAssign').disabled = false;
            document.getElementById('btnConfirmAssign').textContent = 'Sí, asignar';
        }
    });

    /* ── Refresh cada 15s ── */
    document.getElementById('btnRefresh').addEventListener('click', loadCandidates);
    setInterval(loadCandidates, 15000);
    loadCandidates();
})();
</script>
@endsection
