@extends('layouts.admin')

@section('title', 'Diagnóstico de rutas — OSRM')
@section('eyebrow', 'Herramientas')
@section('page-title', 'Diagnóstico de rutas')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.min.css') }}">
@endpush

@section('content')
<section class="admin-panel">
    <div class="admin-panel-header">
        <div>
            <h2>Calculadora de rutas OSRM</h2>
            <p>
                Herramienta de diagnóstico para verificar el cálculo de rutas viales reales.
                Haz clic en el mapa o introduce coordenadas manualmente.
            </p>
        </div>
        <a class="admin-button" href="{{ route('admin.dashboard') }}">← Panel</a>
    </div>

    {{-- Barra de modo de clic --}}
    <div class="diag-mode-bar">
        <span class="diag-mode-label">Modo de clic:</span>
        <button type="button" id="btn-mode-origin" class="diag-mode-btn is-active-origin">
            <i class="bi bi-circle-fill" style="color:#22c55e;font-size:.65rem"></i> Origen
        </button>
        <button type="button" id="btn-mode-dest" class="diag-mode-btn">
            <i class="bi bi-circle-fill" style="color:#ef4444;font-size:.65rem"></i> Destino
        </button>
        <span style="margin-left:auto;display:flex;gap:.5rem;flex-wrap:wrap">
            <button type="button" id="btn-preset-satipo" class="admin-button" style="font-size:.78rem;padding:.3rem .75rem">
                <i class="bi bi-geo-alt"></i> Preset Satipo
            </button>
            <button type="button" id="btn-limpiar" class="admin-button admin-button-logout" style="font-size:.78rem;padding:.3rem .75rem">
                <i class="bi bi-trash"></i> Limpiar
            </button>
        </span>
    </div>

    {{-- Tarjetas de coordenadas --}}
    <div class="diag-controls">
        <div class="diag-point-card is-origin">
            <h4><i class="bi bi-record-circle-fill"></i> Punto de origen</h4>
            <div class="diag-coord-grid">
                <label>
                    Latitud
                    <input type="number" id="orig-lat" step="0.00000001" placeholder="-11.2534">
                </label>
                <label>
                    Longitud
                    <input type="number" id="orig-lng" step="0.00000001" placeholder="-74.6362">
                </label>
            </div>
        </div>
        <div class="diag-point-card is-dest">
            <h4><i class="bi bi-flag-fill"></i> Punto de destino</h4>
            <div class="diag-coord-grid">
                <label>
                    Latitud
                    <input type="number" id="dest-lat" step="0.00000001" placeholder="-11.2597">
                </label>
                <label>
                    Longitud
                    <input type="number" id="dest-lng" step="0.00000001" placeholder="-74.6276">
                </label>
            </div>
        </div>
    </div>

    {{-- Mapa Leaflet --}}
    <div id="mapa-diagnostico"></div>

    {{-- Botón calcular --}}
    <div class="diag-actions">
        <button type="button" id="btn-calcular" class="admin-button admin-button-primary">
            <i class="bi bi-arrow-right-circle"></i> Calcular ruta
        </button>
    </div>

    {{-- Alerta sin ruta --}}
    <div class="diag-no-route" id="diag-no-route">
        <i class="bi bi-exclamation-triangle-fill"></i>
        No se encontró ruta vial entre los puntos seleccionados.
    </div>

    {{-- Panel de resultados --}}
    <div class="diag-result-panel" id="diag-result-panel">
        <div class="diag-result-card card-dist">
            <div class="diag-rc-label"><i class="bi bi-rulers"></i> Distancia vial</div>
            <div class="diag-rc-value" id="r-km">—</div>
            <div class="diag-rc-sub" id="r-m">— metros</div>
        </div>
        <div class="diag-result-card card-time">
            <div class="diag-rc-label"><i class="bi bi-clock"></i> Tiempo estimado</div>
            <div class="diag-rc-value" id="r-min">—</div>
            <div class="diag-rc-sub" id="r-seg">— segundos</div>
        </div>
        <div class="diag-result-card card-prov">
            <div class="diag-rc-label"><i class="bi bi-server"></i> Proveedor</div>
            <div class="diag-rc-value" style="font-size:1.1rem" id="r-prov">—</div>
            <div class="diag-rc-sub">Ruta vial real</div>
        </div>
    </div>

    {{-- Nota informativa --}}
    <div class="admin-alert" style="margin-top:1.5rem;background:#eff6ff;border-color:#bfdbfe;color:#1e40af;font-size:.82rem">
        <i class="bi bi-info-circle"></i>
        <span>
            <strong>OSRM</strong> calcula distancia y duración por vías reales, no en línea recta.
            El servidor público <code>router.project-osrm.org</code> es solo para desarrollo.
            Para producción se recomienda instancia propia o proveedor dedicado.
        </span>
    </div>
</section>
@endsection

@push('scripts')
<script>window.GeoConfig = @json($geoConfig);</script>
<script src="{{ asset('vendor/leaflet/leaflet.min.js') }}"></script>
<script>
(function () {
    /* ── Iconos Leaflet locales ──────────────────────── */
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconUrl:       '{{ asset("vendor/leaflet/images/marker-icon.png") }}',
        iconRetinaUrl: '{{ asset("vendor/leaflet/images/marker-icon-2x.png") }}',
        shadowUrl:     '{{ asset("vendor/leaflet/images/marker-shadow.png") }}',
    });

    /* ── Iconos de colores para origen / destino ─────── */
    function colorIcon(color) {
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 41" width="25" height="41">
            <path fill="${color}" stroke="#fff" stroke-width="1.5"
                d="M12.5 0C5.6 0 0 5.6 0 12.5c0 9.4 12.5 28.5 12.5 28.5S25 21.9 25 12.5C25 5.6 19.4 0 12.5 0z"/>
            <circle fill="#fff" cx="12.5" cy="12.5" r="5"/>
        </svg>`;
        return L.divIcon({
            html:        `<div style="line-height:0">${svg}</div>`,
            iconSize:    [25, 41],
            iconAnchor:  [12, 41],
            popupAnchor: [0, -38],
            className:   '',
        });
    }
    const iconOrigen = colorIcon('#22c55e');
    const iconDestino = colorIcon('#ef4444');

    /* ── Centrar en Satipo ───────────────────────────── */
    const SATIPO = { lat: -11.2534, lng: -74.6362, zoom: 14 };

    const map = L.map('mapa-diagnostico').setView([SATIPO.lat, SATIPO.lng], SATIPO.zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    /* ── Estado ──────────────────────────────────────── */
    let clickMode     = 'origin'; // 'origin' | 'destination'
    let markerOrigen  = null;
    let markerDestino = null;
    let routePolyline = null;

    /* ── Inputs ──────────────────────────────────────── */
    const iOrigLat = document.getElementById('orig-lat');
    const iOrigLng = document.getElementById('orig-lng');
    const iDestLat = document.getElementById('dest-lat');
    const iDestLng = document.getElementById('dest-lng');

    /* ── Botones de modo ─────────────────────────────── */
    const btnModeOrigin = document.getElementById('btn-mode-origin');
    const btnModeDest   = document.getElementById('btn-mode-dest');

    function setMode(mode) {
        clickMode = mode;
        btnModeOrigin.className = 'diag-mode-btn' + (mode === 'origin' ? ' is-active-origin' : '');
        btnModeDest.className   = 'diag-mode-btn' + (mode === 'destination' ? ' is-active-dest' : '');
        map.getContainer().style.cursor = 'crosshair';
    }
    btnModeOrigin.addEventListener('click', () => setMode('origin'));
    btnModeDest.addEventListener('click', () => setMode('destination'));

    /* ── Clic en el mapa ─────────────────────────────── */
    map.on('click', e => {
        const { lat, lng } = e.latlng;
        if (clickMode === 'origin') {
            setOrigen(lat, lng);
            setMode('destination'); /* avanza automáticamente al modo destino */
        } else {
            setDestino(lat, lng);
        }
    });

    function setOrigen(lat, lng) {
        iOrigLat.value = lat.toFixed(8);
        iOrigLng.value = lng.toFixed(8);
        if (markerOrigen) markerOrigen.setLatLng([lat, lng]);
        else              markerOrigen = L.marker([lat, lng], { icon: iconOrigen, draggable: true }).addTo(map);
        markerOrigen.off('dragend').on('dragend', () => {
            const p = markerOrigen.getLatLng();
            iOrigLat.value = p.lat.toFixed(8);
            iOrigLng.value = p.lng.toFixed(8);
        });
    }

    function setDestino(lat, lng) {
        iDestLat.value = lat.toFixed(8);
        iDestLng.value = lng.toFixed(8);
        if (markerDestino) markerDestino.setLatLng([lat, lng]);
        else               markerDestino = L.marker([lat, lng], { icon: iconDestino, draggable: true }).addTo(map);
        markerDestino.off('dragend').on('dragend', () => {
            const p = markerDestino.getLatLng();
            iDestLat.value = p.lat.toFixed(8);
            iDestLng.value = p.lng.toFixed(8);
        });
    }

    /* ── Preset Satipo: Municipalidad → Posta de Salud ── */
    document.getElementById('btn-preset-satipo').addEventListener('click', () => {
        setOrigen(-11.25342, -74.63621);   /* Municipalidad Provincial de Satipo */
        setDestino(-11.25974, -74.62764);  /* Posta de salud Satipo */
        clearRoute();
        map.fitBounds([
            [-11.25342, -74.63621],
            [-11.25974, -74.62764],
        ], { padding: [60, 60] });
    });

    /* ── Limpiar ─────────────────────────────────────── */
    document.getElementById('btn-limpiar').addEventListener('click', () => {
        if (markerOrigen)  { map.removeLayer(markerOrigen);  markerOrigen = null; }
        if (markerDestino) { map.removeLayer(markerDestino); markerDestino = null; }
        iOrigLat.value = iOrigLng.value = iDestLat.value = iDestLng.value = '';
        clearRoute();
        setMode('origin');
    });

    function clearRoute() {
        if (routePolyline) { map.removeLayer(routePolyline); routePolyline = null; }
        document.getElementById('diag-result-panel').classList.remove('is-visible');
        document.getElementById('diag-no-route').classList.remove('is-visible');
    }

    /* ── Calcular ruta ───────────────────────────────── */
    document.getElementById('btn-calcular').addEventListener('click', calcularRuta);

    async function calcularRuta() {
        const origLat = parseFloat(iOrigLat.value);
        const origLng = parseFloat(iOrigLng.value);
        const destLat = parseFloat(iDestLat.value);
        const destLng = parseFloat(iDestLng.value);

        if ([origLat, origLng, destLat, destLng].some(isNaN)) {
            alert('Selecciona origen y destino en el mapa o introduce las coordenadas.');
            return;
        }

        const btn = document.getElementById('btn-calcular');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Calculando…';
        clearRoute();

        try {
            const res = await fetch('{{ route("admin.diagnostico.rutas.calcular") }}', {
                method:  'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'Accept':        'application/json',
                    'X-CSRF-TOKEN':  '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    origin_latitude:       origLat,
                    origin_longitude:      origLng,
                    destination_latitude:  destLat,
                    destination_longitude: destLng,
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.error ?? 'Error del servidor.');
            }

            mostrarResultados(data);

        } catch (err) {
            alert('Error al calcular la ruta: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-right-circle"></i> Calcular ruta';
        }
    }

    function mostrarResultados(data) {
        if (!data.route_found) {
            document.getElementById('diag-no-route').classList.add('is-visible');
            return;
        }

        /* Dibujar polilínea — OSRM devuelve [[lng, lat]], Leaflet necesita [[lat, lng]] */
        const coords = data.geometry.map(([lng, lat]) => [lat, lng]);
        routePolyline = L.polyline(coords, {
            color:   '#2563eb',
            weight:  5,
            opacity: .85,
        }).addTo(map);
        map.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });

        /* Rellenar tarjetas */
        const km  = data.distance_kilometers;
        const min = data.duration_minutes;

        document.getElementById('r-km').textContent =
            km >= 1 ? km.toFixed(2) + ' km' : Math.round(data.distance_meters) + ' m';
        document.getElementById('r-m').textContent =
            Math.round(data.distance_meters).toLocaleString('es-PE') + ' metros';

        document.getElementById('r-min').textContent =
            min >= 1 ? Math.ceil(min) + ' min' : Math.round(data.duration_seconds) + ' s';
        document.getElementById('r-seg').textContent =
            Math.round(data.duration_seconds) + ' segundos';

        document.getElementById('r-prov').textContent =
            data.provider.toUpperCase();

        document.getElementById('diag-result-panel').classList.add('is-visible');
    }

    /* ── Cursor crosshair inicial ────────────────────── */
    setMode('origin');

})();
</script>
@endpush
