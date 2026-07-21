@extends('layouts.admin')

@section('title', $zone->exists ? 'Editar zona' : 'Nueva zona')
@section('eyebrow', 'Zonas y tarifas')
@section('page-title', $zone->exists ? 'Editar zona de delivery' : 'Nueva zona de delivery')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-header">
        <div>
            <h2>{{ $zone->exists ? 'Actualizar zona' : 'Crear zona' }}</h2>
            <p>Define el polígono de cobertura y las reglas de tarificación.</p>
        </div>
        <a class="admin-button" href="{{ route('admin.settings.index') }}">Volver</a>
    </div>

    <form class="admin-form" method="POST" action="{{ $action }}" id="zone-form">
        @csrf
        @if ($method === 'PUT') @method('PUT') @endif

        {{-- ═══════════════════════════════════════════════════════
             SECCIÓN 1 · INFORMACIÓN BÁSICA
        ════════════════════════════════════════════════════════ --}}
        <h3 class="zone-section-title">Información básica</h3>
        <div class="admin-form-grid">
            <label class="admin-field">
                <span>Nombre *</span>
                <input type="text" name="nombre" value="{{ old('nombre', $zone->nombre) }}" required>
                @error('nombre') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Estado</span>
                <select name="activo">
                    <option value="1" @selected(old('activo', $zone->activo) == true)>Activa</option>
                    <option value="0" @selected(old('activo', $zone->activo) == false)>Inactiva</option>
                </select>
            </label>

            <label class="admin-field">
                <span>Prioridad <small>(menor = gana en solapamiento)</small></span>
                <input type="number" name="prioridad" min="1" max="99" value="{{ old('prioridad', $zone->prioridad ?? 10) }}">
                @error('prioridad') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field admin-field-wide">
                <span>Descripción de cobertura</span>
                <textarea name="descripcion_cobertura" rows="2">{{ old('descripcion_cobertura', $zone->descripcion_cobertura) }}</textarea>
                @error('descripcion_cobertura') <small>{{ $message }}</small> @enderror
            </label>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             SECCIÓN 2 · POLÍGONO DE COBERTURA
        ════════════════════════════════════════════════════════ --}}
        <h3 class="zone-section-title">Área de cobertura</h3>
        <p class="zone-section-desc">
            Haz clic en el mapa para agregar vértices. Se necesitan al menos 3 puntos para definir el polígono.
        </p>

        <div class="zone-editor-wrap">
            <div class="zone-editor-toolbar">
                <span class="zone-editor-status" id="zoneStatus">
                    @if($zone->exists && $zone->tienePoligono())
                        Polígono cargado ({{ count($zone->polygon) }} vértices)
                    @else
                        Sin polígono — haz clic en el mapa para comenzar
                    @endif
                </span>
                <div class="zone-editor-actions">
                    <button type="button" class="admin-button admin-button-sm" id="btnUndo">Deshacer último</button>
                    <button type="button" class="admin-button admin-button-sm admin-button-danger" id="btnClear">Limpiar</button>
                    <button type="button" class="admin-button admin-button-sm" id="btnPreset">
                        Satipo centro
                    </button>
                </div>
            </div>
            <div id="zoneMap" class="zone-editor-map"></div>
        </div>

        <input type="hidden" name="polygon_json" id="polygonJson"
               value="{{ old('polygon_json', $zone->exists && $zone->polygon ? json_encode($zone->polygon) : '') }}">
        @error('polygon_json') <small class="field-error">{{ $message }}</small> @enderror

        {{-- ═══════════════════════════════════════════════════════
             SECCIÓN 3 · TARIFICACIÓN
        ════════════════════════════════════════════════════════ --}}
        <h3 class="zone-section-title">Tarificación</h3>
        <div class="admin-form-grid">
            <label class="admin-field">
                <span>Costo delivery base (S/) *</span>
                <input type="number" name="costo_delivery" min="0" step="0.10"
                       value="{{ old('costo_delivery', $zone->costo_delivery) }}" required>
                @error('costo_delivery') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Km incluidos en el precio base</span>
                <input type="number" name="km_incluidos" min="0" step="0.1"
                       value="{{ old('km_incluidos', $zone->km_incluidos ?? '0') }}">
                @error('km_incluidos') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Precio por km extra (S/)</span>
                <input type="number" name="precio_por_km_extra" min="0" step="0.10"
                       value="{{ old('precio_por_km_extra', $zone->precio_por_km_extra ?? '0') }}">
                @error('precio_por_km_extra') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Recargo de zona (S/)</span>
                <input type="number" name="recargo" min="0" step="0.10"
                       value="{{ old('recargo', $zone->recargo ?? '0') }}">
                @error('recargo') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Delivery gratis desde (S/)</span>
                <input type="number" name="delivery_gratis_desde" min="0" step="0.50"
                       value="{{ old('delivery_gratis_desde', $zone->delivery_gratis_desde) }}"
                       placeholder="Vacío = nunca gratis">
                @error('delivery_gratis_desde') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Pedido mínimo (S/)</span>
                <input type="number" name="pedido_minimo" min="0" step="0.50"
                       value="{{ old('pedido_minimo', $zone->pedido_minimo) }}"
                       placeholder="Vacío = sin mínimo">
                @error('pedido_minimo') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Distancia máxima (km)</span>
                <input type="number" name="distancia_maxima_km" min="0.1" step="0.1"
                       value="{{ old('distancia_maxima_km', $zone->distancia_maxima_km) }}"
                       placeholder="Vacío = sin límite">
                @error('distancia_maxima_km') <small>{{ $message }}</small> @enderror
            </label>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             SECCIÓN 4 · TIEMPOS
        ════════════════════════════════════════════════════════ --}}
        <h3 class="zone-section-title">Tiempos estimados</h3>
        <div class="admin-form-grid">
            <label class="admin-field">
                <span>Mínimo (min)</span>
                <input type="number" name="tiempo_estimado_min" min="1" max="300"
                       value="{{ old('tiempo_estimado_min', $zone->tiempo_estimado_min) }}"
                       placeholder="ej. 20">
                @error('tiempo_estimado_min') <small>{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Máximo (min)</span>
                <input type="number" name="tiempo_estimado_max" min="1" max="300"
                       value="{{ old('tiempo_estimado_max', $zone->tiempo_estimado_max) }}"
                       placeholder="ej. 45">
                @error('tiempo_estimado_max') <small>{{ $message }}</small> @enderror
            </label>
        </div>

        <div class="admin-form-actions">
            <button class="admin-button admin-button-dark" type="submit" id="submitBtn">
                {{ $zone->exists ? 'Guardar cambios' : 'Crear zona' }}
            </button>
        </div>
    </form>
</section>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<script>window.GeoConfig = @json($geoConfig);</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLs=" crossorigin=""></script>

<script>
(function () {
    /* ── Estado del editor ── */
    const vertices = [];          // [[lng, lat], ...] — GeoJSON order
    let map, polyline, polygon, markers = [];

    /* ── Punto inicial: Satipo ── */
    const CENTER = [-11.2534, -74.6362];

    /* ── Iniciar mapa ── */
    map = L.map('zoneMap', { zoomControl: true }).setView(CENTER, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    /* ── Cargar polígono existente (edición) ── */
    const existing = document.getElementById('polygonJson').value;
    if (existing) {
        try {
            const pts = JSON.parse(existing);
            pts.forEach(([lng, lat]) => addVertex(lat, lng, false));
            redraw();
            fitPolygon();
        } catch (e) { /* JSON inválido — ignorar */ }
    }

    /* ── Click en mapa = agregar vértice ── */
    map.on('click', function (e) {
        addVertex(e.latlng.lat, e.latlng.lng);
    });

    function addVertex(lat, lng, doRedraw = true) {
        vertices.push([lng, lat]);        // guardamos [lng, lat] (GeoJSON order)

        const icon = L.divIcon({
            className: '',
            html: `<div class="zone-vertex-dot" style="--n:${vertices.length}"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10],
        });
        const marker = L.marker([lat, lng], { icon, interactive: false }).addTo(map);
        markers.push(marker);

        if (doRedraw) redraw();
    }

    function redraw() {
        /* Eliminar capas anteriores */
        if (polyline) { map.removeLayer(polyline); polyline = null; }
        if (polygon)  { map.removeLayer(polygon);  polygon  = null; }

        if (vertices.length < 2) {
            updateStatus();
            syncHidden();
            return;
        }

        /* Convertir a Leaflet order [lat, lng] */
        const latlngs = vertices.map(([lng, lat]) => [lat, lng]);

        if (vertices.length === 2) {
            polyline = L.polyline(latlngs, { color: '#2563eb', weight: 2, dashArray: '6,4' }).addTo(map);
        } else {
            /* Polígono cerrado */
            polygon = L.polygon(latlngs, {
                color: '#2563eb', weight: 2,
                fillColor: '#3b82f6', fillOpacity: 0.20,
            }).addTo(map);
        }

        updateStatus();
        syncHidden();
    }

    function fitPolygon() {
        if (vertices.length < 2) return;
        const latlngs = vertices.map(([lng, lat]) => [lat, lng]);
        map.fitBounds(L.latLngBounds(latlngs), { padding: [30, 30] });
    }

    function syncHidden() {
        const input = document.getElementById('polygonJson');
        input.value = vertices.length >= 3 ? JSON.stringify(vertices) : '';
    }

    function updateStatus() {
        const el = document.getElementById('zoneStatus');
        if (vertices.length === 0) {
            el.textContent = 'Sin polígono — haz clic en el mapa para comenzar';
            el.className = 'zone-editor-status';
        } else if (vertices.length < 3) {
            el.textContent = `${vertices.length} vértice(s) — necesitas al menos 3`;
            el.className = 'zone-editor-status zone-status-warn';
        } else {
            el.textContent = `Polígono listo (${vertices.length} vértices)`;
            el.className = 'zone-editor-status zone-status-ok';
        }
    }

    /* ── Botón Deshacer ── */
    document.getElementById('btnUndo').addEventListener('click', function () {
        if (vertices.length === 0) return;
        vertices.pop();
        const m = markers.pop();
        if (m) map.removeLayer(m);
        redraw();
    });

    /* ── Botón Limpiar ── */
    document.getElementById('btnClear').addEventListener('click', function () {
        vertices.length = 0;
        markers.forEach(m => map.removeLayer(m));
        markers.length = 0;
        redraw();
    });

    /* ── Botón Preset Satipo ── */
    document.getElementById('btnPreset').addEventListener('click', function () {
        // Cuadrado aproximado al centro de Satipo
        const preset = [
            [-74.6420, -11.2490],
            [-74.6290, -11.2490],
            [-74.6290, -11.2580],
            [-74.6420, -11.2580],
        ];
        vertices.length = 0;
        markers.forEach(m => map.removeLayer(m));
        markers.length = 0;
        preset.forEach(([lng, lat]) => addVertex(lat, lng, false));
        redraw();
        fitPolygon();
    });

    /* ── Validar al enviar ── */
    document.getElementById('zone-form').addEventListener('submit', function (e) {
        const val = document.getElementById('polygonJson').value;
        if (!val) {
            // Permitir guardar sin polígono (zona sin cobertura gráfica aún)
            return;
        }
        let pts;
        try { pts = JSON.parse(val); } catch (_) {
            e.preventDefault();
            alert('El polígono contiene datos inválidos. Usa el botón Limpiar y dibuja de nuevo.');
            return;
        }
        if (pts.length < 3) {
            e.preventDefault();
            alert('El polígono necesita al menos 3 vértices para ser válido.');
        }
    });
})();
</script>
@endsection
