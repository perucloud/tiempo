/**
 * TIEMPO — Mapa de tracking del admin
 * Mapa Leaflet con posiciones de repartidores activos.
 * Polling cada 15s via GET a UBICACIONES_URL (definido en la vista).
 */
(function () {
    const REFRESH_MS = 15000;
    const DEFAULT_CENTER = [-12.0464, -77.0428]; /* Lima, Perú */
    const DEFAULT_ZOOM   = 13;

    let map     = null;
    let markers = {};

    function colorByEstado(estado, gpsActivo) {
        if (!gpsActivo) return '#94a3b8';
        return estado === 'disponible' ? '#10b981' : '#f59e0b';
    }

    function buildIcon(estado, gpsActivo) {
        const color = colorByEstado(estado, gpsActivo);
        return L.divIcon({
            html: `<div style="
                background:${color};
                width:16px;height:16px;
                border-radius:50%;
                border:3px solid #fff;
                box-shadow:0 2px 8px rgba(0,0,0,.35);
            "></div>`,
            className: '',
            iconAnchor: [8, 8],
        });
    }

    function initMap() {
        map = L.map('mapa-tracking', {
            center: DEFAULT_CENTER,
            zoom:   DEFAULT_ZOOM,
            zoomControl: true,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(map);
    }

    function updateMarker(courier) {
        const lat = parseFloat(courier.latitud);
        const lng = parseFloat(courier.longitud);
        if (isNaN(lat) || isNaN(lng)) return;

        const icon    = buildIcon(courier.estado, courier.gps_activo);
        const popupHtml = `
            <div style="min-width:160px; font-family:inherit;">
                <strong style="font-size:13px;">${courier.nombre}</strong><br>
                <span style="font-size:12px; color:#64748b;">
                    ${courier.estado === 'disponible' ? '🟢 Disponible' : '🟡 En ruta'}<br>
                    GPS: ${courier.gps_activo ? '✅ Activo' : '⚠️ Sin señal'}<br>
                    ${courier.actualizado_at ?? ''}
                </span>
            </div>
        `;

        if (markers[courier.id]) {
            markers[courier.id].setLatLng([lat, lng]).setIcon(icon).getPopup().setContent(popupHtml);
        } else {
            markers[courier.id] = L.marker([lat, lng], { icon })
                .addTo(map)
                .bindPopup(popupHtml);
        }

        /* Resaltar el card en el sidebar */
        const card = document.querySelector(`[data-id="${courier.id}"]`);
        if (card) {
            card.classList.toggle('is-live', courier.gps_activo);
        }
    }

    function removeStaleMarkers(activeIds) {
        Object.keys(markers).forEach(function (id) {
            if (!activeIds.includes(parseInt(id))) {
                map.removeLayer(markers[id]);
                delete markers[id];
            }
        });
    }

    function updateLastUpdated() {
        const el = document.getElementById('tracking-last-update');
        if (el) el.textContent = 'Actualizado ' + new Date().toLocaleTimeString('es-PE');
    }

    function fetchUbicaciones() {
        if (!window.UBICACIONES_URL) return;

        fetch(window.UBICACIONES_URL, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                const couriers   = json.data ?? [];
                const activeIds  = couriers.map(function (c) { return c.id; });

                couriers.forEach(updateMarker);
                removeStaleMarkers(activeIds);
                updateLastUpdated();

                const countEl = document.getElementById('tracking-count');
                if (countEl) countEl.textContent = couriers.length;
            })
            .catch(function () {
                const el = document.getElementById('tracking-last-update');
                if (el) el.textContent = 'Error al actualizar — reintentando...';
            });
    }

    /* Centrar mapa en el repartidor al hacer click en el card */
    function bindSidebarClicks() {
        document.querySelectorAll('.tracking-courier-card').forEach(function (card) {
            card.addEventListener('click', function () {
                const id = parseInt(card.dataset.id);
                if (markers[id]) {
                    map.setView(markers[id].getLatLng(), 17, { animate: true });
                    markers[id].openPopup();
                    document.querySelectorAll('.tracking-courier-card').forEach(function (c) {
                        c.classList.remove('is-selected');
                    });
                    card.classList.add('is-selected');
                }
            });
        });
    }

    /* Botón de refresh manual */
    const refreshBtn = document.getElementById('tracking-refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', fetchUbicaciones);
    }

    /* Init */
    initMap();
    fetchUbicaciones();
    bindSidebarClicks();
    setInterval(fetchUbicaciones, REFRESH_MS);
})();
