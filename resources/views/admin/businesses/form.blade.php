@extends('layouts.admin')

@section('title', $business->exists ? 'Editar negocio' : 'Nuevo negocio')
@section('eyebrow', 'Red comercial')
@section('page-title', $business->exists ? 'Editar negocio afiliado' : 'Nuevo negocio afiliado')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.min.css') }}">
@endpush

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $business->exists ? 'Actualizar información comercial' : 'Crear negocio afiliado' }}</h2>
                <p>Completa los 5 pasos para registrar toda la información del negocio.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.businesses.index') }}">Volver</a>
        </div>

        {{-- Wizard nav --}}
        <div class="wizard-nav" id="wizardNav">
            <div class="wizard-step active" data-step="1">
                <span class="ws-num">1</span><span>Básicos</span>
            </div>
            <div class="wizard-step" data-step="2">
                <span class="ws-num">2</span><span>Visual</span>
            </div>
            <div class="wizard-step" data-step="3">
                <span class="ws-num">3</span><span>Ubicación</span>
            </div>
            <div class="wizard-step" data-step="4">
                <span class="ws-num">4</span><span>Contacto</span>
            </div>
            <div class="wizard-step" data-step="5">
                <span class="ws-num">5</span><span>Social</span>
            </div>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}" id="wizardForm">
            @csrf
            @if ($method === 'PUT') @method('PUT') @endif

            @if($errors->any())
                <div class="admin-alert admin-alert-error">
                    Por favor corrige los errores marcados en el formulario.
                </div>
            @endif

            {{-- ═══════════════════════════════════════
                 PASO 1 — Datos básicos
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel active" id="step-1">
                <h3 class="wizard-section-title">
                    <i class="bi bi-shop"></i> Datos del negocio
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Nombre comercial *</span>
                        <input type="text" name="nombre_comercial"
                               value="{{ old('nombre_comercial', $business->nombre_comercial) }}"
                               required placeholder="Ej: Pollos El Gordo">
                        @error('nombre_comercial') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Tipo de negocio *</span>
                        <select name="tipo_negocio" required>
                            @foreach ($tipoOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('tipo_negocio', $business->tipo_negocio) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_negocio') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>RUC</span>
                        <input type="text" name="ruc"
                               value="{{ old('ruc', $business->ruc) }}"
                               placeholder="20123456789">
                        @error('ruc') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Estado *</span>
                        <select name="estado" required>
                            @foreach ($estadoOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('estado', $business->estado) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('estado') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Atención ahora *</span>
                        <select name="abierto" required>
                            <option value="1" @selected((string) old('abierto', (int) $business->abierto) === '1')>Abierto</option>
                            <option value="0" @selected((string) old('abierto', (int) $business->abierto) === '0')>Cerrado</option>
                        </select>
                        @error('abierto') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Tiempo de preparación (min)</span>
                        <input type="number" name="tiempo_preparacion" min="0" max="240"
                               value="{{ old('tiempo_preparacion', $business->tiempo_preparacion) }}"
                               placeholder="20">
                        @error('tiempo_preparacion') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Hora apertura</span>
                        <input type="time" name="hora_apertura"
                               value="{{ old('hora_apertura', $business->hora_apertura) }}">
                        @error('hora_apertura') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Hora cierre</span>
                        <input type="time" name="hora_cierre"
                               value="{{ old('hora_cierre', $business->hora_cierre) }}">
                        @error('hora_cierre') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 2 — Identidad visual
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-2">
                <h3 class="wizard-section-title">
                    <i class="bi bi-palette"></i> Identidad visual
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field admin-field-wide">
                        <span>Imagen principal (URL o ruta)</span>
                        <input type="text" name="imagen"
                               value="{{ old('imagen', $business->imagen) }}"
                               placeholder="https://... o /images/negocios/foto.jpg">
                        @error('imagen') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Slogan <small style="font-weight:400;color:#888">(máx. 4 palabras)</small></span>
                        <input type="text" name="slogan"
                               value="{{ old('slogan', $business->slogan) }}"
                               placeholder="Lo mejor del sabor">
                        @error('slogan') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Precio mínimo de pedido (S/)</span>
                        <input type="number" name="precio_minimo" step="0.50" min="0"
                               value="{{ old('precio_minimo', $business->precio_minimo) }}"
                               placeholder="15.00">
                        @error('precio_minimo') <small>{{ $message }}</small> @enderror
                    </label>

                    <div class="admin-field">
                        <span>Color de marca <small style="font-weight:400;color:#888">(hex)</small></span>
                        <div class="wizard-color-row">
                            <input type="color" id="color_marca_picker"
                                   value="{{ old('color_marca', $business->color_marca ?? '#CC3D00') }}">
                            <input type="text" id="color_marca_text" name="color_marca"
                                   value="{{ old('color_marca', $business->color_marca ?? '#CC3D00') }}"
                                   placeholder="#CC3D00">
                        </div>
                        @error('color_marca') <small>{{ $message }}</small> @enderror
                    </div>

                    <label class="admin-field admin-field-wide">
                        <span>Descripción</span>
                        <textarea name="descripcion" rows="4"
                                  placeholder="Describe brevemente el negocio...">{{ old('descripcion', $business->descripcion) }}</textarea>
                        @error('descripcion') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 3 — Ubicación (mapa)
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-3">
                <h3 class="wizard-section-title">
                    <i class="bi bi-geo-alt"></i> Ubicación del negocio
                </h3>

                {{-- Buscador de dirección con autocompletado --}}
                <div class="map-search-wrap">
                    <div class="map-search-row">
                        <input type="text" id="mapSearchInput"
                               placeholder="Escribe una dirección, calle o lugar…"
                               autocomplete="off"
                               aria-label="Buscar dirección"
                               aria-autocomplete="list"
                               aria-controls="map-autocomplete"
                               role="combobox"
                               aria-expanded="false">
                        <button type="button" id="btnMapSearch" title="Buscar primer resultado">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                    <div id="map-autocomplete"
                         class="map-autocomplete"
                         role="listbox"
                         aria-label="Sugerencias de dirección"
                         hidden></div>
                </div>

                <div class="map-hint">
                    <i class="bi bi-hand-index-thumb"></i>
                    Haz clic en el mapa para marcar la ubicación exacta del negocio. También puedes arrastrar el marcador.
                </div>

                {{-- Mapa --}}
                <div id="mapa-negocio"></div>

                {{-- Coordenadas capturadas --}}
                <div class="map-coords-row">
                    <label class="admin-field">
                        <span>Latitud</span>
                        <input type="number" id="latitud" name="latitud" step="0.00000001"
                               value="{{ old('latitud', $business->latitud) }}"
                               placeholder="-12.046374">
                        @error('latitud') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="admin-field">
                        <span>Longitud</span>
                        <input type="number" id="longitud" name="longitud" step="0.00000001"
                               value="{{ old('longitud', $business->longitud) }}"
                               placeholder="-77.042793">
                        @error('longitud') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                {{-- Estado de geocodificación inversa --}}
                <div id="geocode-status" class="geocode-status">
                    <span class="gs-loading"><i class="bi bi-arrow-repeat"></i> Obteniendo dirección…</span>
                    <span class="gs-done"><i class="bi bi-check-circle"></i> Dirección actualizada — puedes editarla</span>
                    <span class="gs-error"><i class="bi bi-exclamation-circle"></i> No se pudo obtener la dirección</span>
                </div>

                @if(!$business->latitud)
                    <p class="map-no-coords" id="map-no-coords-hint">
                        <i class="bi bi-info-circle"></i>
                        Sin coordenadas aún — haz clic en el mapa o escribe los valores manualmente.
                    </p>
                @endif

                {{-- Campos de dirección — autocompletables por geocodificación inversa --}}
                <h3 class="wizard-section-title" style="margin-top:1.75rem">
                    <i class="bi bi-signpost"></i> Dirección
                    <small style="font-size:.72rem;font-weight:400;color:var(--admin-muted);margin-left:.5rem">
                        Se completa automáticamente al marcar en el mapa
                    </small>
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Dirección</span>
                        <input type="text" id="gc-direccion" name="direccion"
                               value="{{ old('direccion', $business->direccion) }}"
                               placeholder="Av. Principal 123">
                        @error('direccion') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Distrito</span>
                        <input type="text" id="gc-distrito" name="distrito"
                               value="{{ old('distrito', $business->distrito) }}"
                               placeholder="Miraflores">
                        @error('distrito') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Provincia</span>
                        <input type="text" id="gc-provincia" name="provincia"
                               value="{{ old('provincia', $business->provincia) }}"
                               placeholder="Lima">
                        @error('provincia') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Departamento</span>
                        <input type="text" id="gc-departamento" name="departamento"
                               value="{{ old('departamento', $business->departamento) }}"
                               placeholder="Lima">
                        @error('departamento') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Código postal</span>
                        <input type="text" id="gc-codigo_postal" name="codigo_postal"
                               value="{{ old('codigo_postal', $business->codigo_postal) }}"
                               placeholder="15074">
                        @error('codigo_postal') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>País</span>
                        <input type="text" id="gc-pais" name="pais"
                               value="{{ old('pais', $business->pais ?? 'Perú') }}"
                               placeholder="Perú">
                        @error('pais') <small>{{ $message }}</small> @enderror
                    </label>

                    {{-- Referencia: siempre manual, nunca autocompletada --}}
                    <label class="admin-field admin-field-wide">
                        <span>Referencia <small style="font-weight:400;color:#888">(manual)</small></span>
                        <input type="text" name="referencia"
                               value="{{ old('referencia', $business->referencia) }}"
                               placeholder="Al frente del parque, local rojo">
                        @error('referencia') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 4 — Contacto
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-4">
                <h3 class="wizard-section-title">
                    <i class="bi bi-telephone"></i> Información de contacto
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Celular</span>
                        <input type="text" name="celular"
                               value="{{ old('celular', $business->celular) }}"
                               placeholder="987 654 321">
                        @error('celular') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>WhatsApp</span>
                        <input type="text" name="whatsapp"
                               value="{{ old('whatsapp', $business->whatsapp) }}"
                               placeholder="987 654 321">
                        @error('whatsapp') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Teléfono fijo</span>
                        <input type="text" name="telefono_fijo"
                               value="{{ old('telefono_fijo', $business->telefono_fijo) }}"
                               placeholder="01 234 5678">
                        @error('telefono_fijo') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Teléfono principal (historial)</span>
                        <input type="text" name="telefono"
                               value="{{ old('telefono', $business->telefono) }}"
                               placeholder="987 000 000">
                        @error('telefono') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Correo electrónico</span>
                        <input type="email" name="email"
                               value="{{ old('email', $business->email) }}"
                               placeholder="negocio@ejemplo.com">
                        @error('email') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Página web</span>
                        <input type="text" name="pagina_web"
                               value="{{ old('pagina_web', $business->pagina_web) }}"
                               placeholder="https://minegocio.com">
                        @error('pagina_web') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 5 — Redes sociales
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-5">
                <h3 class="wizard-section-title">
                    <i class="bi bi-share"></i> Redes sociales
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Facebook</span>
                        <input type="text" name="facebook"
                               value="{{ old('facebook', $business->facebook) }}"
                               placeholder="https://facebook.com/minegocio">
                        @error('facebook') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Instagram</span>
                        <input type="text" name="instagram"
                               value="{{ old('instagram', $business->instagram) }}"
                               placeholder="https://instagram.com/minegocio">
                        @error('instagram') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>TikTok</span>
                        <input type="text" name="tiktok"
                               value="{{ old('tiktok', $business->tiktok) }}"
                               placeholder="https://tiktok.com/@minegocio">
                        @error('tiktok') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="admin-alert" style="margin-top:1.5rem;background:#eff6ff;border-color:#bfdbfe;color:#1e40af">
                    <strong>¡Todo listo!</strong>
                    Revisa la información y guarda el negocio.
                    Los campos vacíos se pueden completar más adelante desde <em>Editar</em>.
                </div>
            </div>

            {{-- Wizard footer --}}
            <div class="wizard-footer">
                <button type="button" class="admin-button admin-button-logout" id="btn-prev">
                    ← Anterior
                </button>
                <div class="wizard-footer-right">
                    <button type="button" class="admin-button admin-button-primary" id="btn-next">
                        Siguiente →
                    </button>
                    <button type="submit" class="admin-button admin-button-dark wizard-hidden" id="btn-submit">
                        <i class="bi bi-check-lg"></i>
                        {{ $business->exists ? 'Guardar cambios' : 'Crear negocio' }}
                    </button>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
<script>window.GeoConfig = @json($geoConfig);</script>
<script src="{{ asset('vendor/leaflet/leaflet.min.js') }}"></script>
<script>
(function () {
    /* ─── Wizard navigation ──────────────────────────────── */
    const TOTAL   = 5;
    let current   = 1;

    const wSteps  = document.querySelectorAll('.wizard-step');
    const wPanels = document.querySelectorAll('.wizard-panel');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnSub  = document.getElementById('btn-submit');

    function show(n) {
        n = Math.max(1, Math.min(TOTAL, n));
        wSteps.forEach((s, i) => {
            s.classList.toggle('active', i + 1 === n);
            s.classList.toggle('done',   i + 1 < n);
        });
        wPanels.forEach((p, i) => p.classList.toggle('active', i + 1 === n));
        btnPrev.style.visibility = n === 1 ? 'hidden' : 'visible';
        btnNext.classList.toggle('wizard-hidden', n === TOTAL);
        btnSub.classList.toggle('wizard-hidden',  n !== TOTAL);
        current = n;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (n === 3) {
            /* Esperar a que el panel sea visible antes de init */
            requestAnimationFrame(() => requestAnimationFrame(() => {
                setTimeout(initMap, 100);
            }));
        }
    }

    btnNext.addEventListener('click', () => show(current + 1));
    btnPrev.addEventListener('click', () => show(current - 1));
    wSteps.forEach((s, i) => s.addEventListener('click', () => show(i + 1)));

    /* ─── Auto-jump al paso con errores ─────────────────── */
    const stepFields = {
        1: ['nombre_comercial','tipo_negocio','ruc','estado','abierto','hora_apertura','hora_cierre','tiempo_preparacion'],
        2: ['imagen','slogan','precio_minimo','color_marca','descripcion'],
        3: ['departamento','provincia','distrito','direccion','referencia','latitud','longitud'],
        4: ['celular','whatsapp','telefono_fijo','telefono','email','pagina_web'],
        5: ['facebook','instagram','tiktok'],
    };
    let jumpTo = null;
    document.querySelectorAll('[name]').forEach(el => {
        if (!el.closest('.admin-field')?.querySelector('small')) return;
        for (const [step, fields] of Object.entries(stepFields)) {
            if (fields.includes(el.name)) {
                const s = parseInt(step);
                if (!jumpTo || s < jumpTo) jumpTo = s;
                break;
            }
        }
    });
    show(jumpTo || 1);

    /* ─── Color picker sync ──────────────────────────────── */
    const picker = document.getElementById('color_marca_picker');
    const cText  = document.getElementById('color_marca_text');
    if (picker && cText) {
        picker.addEventListener('input', () => { cText.value = picker.value; });
        cText.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(cText.value)) picker.value = cText.value;
        });
    }

    /* ─── Mapa Leaflet ───────────────────────────────────── */
    let mapObj    = null;
    let mapMarker = null;
    let mapReady  = false;

    const initLat = parseFloat(document.getElementById('latitud')?.value);
    const initLng = parseFloat(document.getElementById('longitud')?.value);

    /* Fijar rutas de iconos (evita autodetección que falla en local) */
    function fixLeafletIcons() {
        delete L.Icon.Default.prototype._getIconUrl;
        L.Icon.Default.mergeOptions({
            iconUrl:       '{{ asset("vendor/leaflet/images/marker-icon.png") }}',
            iconRetinaUrl: '{{ asset("vendor/leaflet/images/marker-icon-2x.png") }}',
            shadowUrl:     '{{ asset("vendor/leaflet/images/marker-shadow.png") }}',
        });
    }

    function showMapError(msg) {
        const el = document.getElementById('mapa-negocio');
        if (el) {
            el.style.cssText = 'display:flex;align-items:center;justify-content:center;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;height:420px;';
            el.innerHTML = '<div style="text-align:center;color:#dc2626;font-weight:600;padding:2rem">'
                + '<i class="bi bi-exclamation-triangle" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>'
                + msg + '</div>';
        }
    }

    function initMap() {
        if (mapReady) {
            mapObj.invalidateSize();
            return;
        }

        if (typeof L === 'undefined') {
            showMapError('No se pudo cargar el mapa.<br>Recarga la página.');
            return;
        }

        try {
            fixLeafletIcons();

            const hasCoords = !isNaN(initLat) && !isNaN(initLng);
            const center    = hasCoords ? [initLat, initLng] : [-9.19, -75.01];
            const zoom      = hasCoords ? 16 : 6;

            mapObj = L.map('mapa-negocio', { zoomControl: true }).setView(center, zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(mapObj);

            if (hasCoords) {
                placeMarker(initLat, initLng);
            }

            mapObj.on('click', e => {
                placeMarker(e.latlng.lat, e.latlng.lng);
                scheduleReverseGeocode(e.latlng.lat, e.latlng.lng);
            });

            mapReady = true;

            /* Segundo invalidate por si el panel aún estaba transitando */
            setTimeout(() => mapObj.invalidateSize(), 300);

        } catch (err) {
            showMapError('Error al inicializar el mapa.<br><small>' + err.message + '</small>');
        }
    }

    function placeMarker(lat, lng) {
        if (!mapObj) return;
        if (mapMarker) {
            mapMarker.setLatLng([lat, lng]);
        } else {
            mapMarker = L.marker([lat, lng], { draggable: true }).addTo(mapObj);
            mapMarker.on('dragend', () => {
                const pos = mapMarker.getLatLng();
                fillCoords(pos.lat, pos.lng);
                scheduleReverseGeocode(pos.lat, pos.lng);
            });
        }
        fillCoords(lat, lng);
    }

    function fillCoords(lat, lng) {
        const elLat = document.getElementById('latitud');
        const elLng = document.getElementById('longitud');
        if (elLat) elLat.value = lat.toFixed(8);
        if (elLng) elLng.value = lng.toFixed(8);
        /* Ocultar hint "sin coordenadas" */
        const hint = document.getElementById('map-no-coords-hint');
        if (hint) hint.style.display = 'none';
    }

    /* Inputs manuales → mover marcador */
    ['latitud', 'longitud'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => {
            const lat = parseFloat(document.getElementById('latitud').value);
            const lng = parseFloat(document.getElementById('longitud').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                if (!mapReady) {
                    initMap();
                    setTimeout(() => {
                        placeMarker(lat, lng);
                        mapObj?.setView([lat, lng], 16);
                        scheduleReverseGeocode(lat, lng);
                    }, 400);
                } else {
                    placeMarker(lat, lng);
                    mapObj.setView([lat, lng], 16);
                    scheduleReverseGeocode(lat, lng);
                }
            }
        });
    });

    /* ─── Geocodificación inversa ────────────────────────── */
    let _gcTimer      = null;
    let _gcController = null;

    /* Mapa click → disparar geocodificación con debounce */
    /* (el click del mapa llega a placeMarker → fillCoords, luego llamamos esto) */

    function scheduleReverseGeocode(lat, lng) {
        clearTimeout(_gcTimer);
        if (_gcController) { _gcController.abort(); _gcController = null; }
        _gcTimer = setTimeout(() => reverseGeocode(lat, lng), 600);
    }

    async function reverseGeocode(lat, lng) {
        setGeocodeStatus('loading');
        _gcController = new AbortController();

        try {
            const url = window.GeoConfig.geocodingBase + '/reverse?'
                + new URLSearchParams({ lat, lon: lng, format: 'json' });

            const res = await fetch(url, {
                signal:  _gcController.signal,
                headers: { 'Accept-Language': 'es' },
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();
            if (!data || !data.address) throw new Error('Sin dirección');

            applyReverseResult(data);
            setGeocodeStatus('done');

        } catch (err) {
            if (err.name !== 'AbortError') {
                setGeocodeStatus('error');
            }
        } finally {
            _gcController = null;
        }
    }

    /**
     * Mapeo del JSON de Nominatim a los campos del formulario.
     * Jerarquía Perú: state=Departamento | county=Provincia | city_district=Distrito
     * Maneja respuestas parciales (campos ausentes → cadena vacía).
     */
    function applyReverseResult(data) {
        const a = data.address ?? {};

        const road     = a.road ?? a.pedestrian ?? a.footway ?? a.path ?? '';
        const houseNum = a.house_number ?? '';
        const calle    = [road, houseNum].filter(Boolean).join(' ');

        const distrito    = a.city_district ?? a.suburb ?? a.municipality ?? a.town ?? a.village ?? '';
        const provincia   = a.county ?? a.city ?? '';
        const departamento = a.state ?? '';
        const codPostal   = a.postcode ?? '';
        const pais        = a.country ?? '';

        gcSetField('gc-direccion',    calle);
        gcSetField('gc-distrito',     distrito);
        gcSetField('gc-provincia',    provincia);
        gcSetField('gc-departamento', departamento);
        gcSetField('gc-codigo_postal', codPostal);
        gcSetField('gc-pais',         pais);
    }

    /** Sobreescribe el campo y añade clase visual "geocoded"; el usuario puede editar libremente. */
    function gcSetField(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = value;
        if (value) {
            el.classList.add('geocoded');
            /* Quitar resaltado al primer keypress del usuario */
            el.addEventListener('input', () => el.classList.remove('geocoded'), { once: true });
        }
    }

    function setGeocodeStatus(state) {
        const el = document.getElementById('geocode-status');
        if (!el) return;
        el.dataset.state = state;
        el.classList.add('is-visible');
        if (state === 'done') {
            /* Ocultar mensaje de éxito después de 4 s */
            setTimeout(() => {
                if (el.dataset.state === 'done') el.classList.remove('is-visible');
            }, 4000);
        }
    }

    /* ══════════════════════════════════════════════════════════
       AUTOCOMPLETADO — configuración centralizada
    ══════════════════════════════════════════════════════════ */
    const NOM = {
        base:        window.GeoConfig.geocodingBase,
        country:     window.GeoConfig.countryCode,
        /* Viewbox centrado en Junín/Satipo como hint de prioridad (bounded=0) */
        viewbox:     '-76.5,-13.0,-73.0,-10.5',
        bounded:     0,
        limit:       7,
        minChars:    3,
        debounceMs:  400,
    };

    const acEl        = document.getElementById('map-autocomplete');
    const searchInput = document.getElementById('mapSearchInput');

    let _acTimer      = null;
    let _acController = null;
    let _acResults    = [];
    let _acFocusIdx   = -1;
    let _acLastQuery  = '';

    /* ─── Renderizar dropdown ──────────────────────────────── */
    function acShow(results) {
        _acResults  = results;
        _acFocusIdx = -1;
        acEl.innerHTML = '';

        if (results.length === 0) {
            acEl.innerHTML =
                '<div class="map-ac-empty">'
                + '<i class="bi bi-geo-alt"></i> Sin resultados — intenta con otro término'
                + '</div>';
        } else {
            results.forEach((r, i) => {
                const parts  = r.display_name.split(',');
                const title  = parts.slice(0, 2).join(',').trim();
                const sub    = parts.slice(2, 5).join(',').trim();
                const item   = document.createElement('div');
                item.className = 'map-ac-item';
                item.setAttribute('role', 'option');
                item.dataset.idx = i;
                item.innerHTML   =
                    `<i class="bi bi-geo-alt-fill"></i>`
                    + `<div><strong>${escHtml(title)}</strong>`
                    + (sub ? `<small>${escHtml(sub)}</small>` : '')
                    + `</div>`;
                /* mousedown para evitar que blur del input se dispare antes del click */
                item.addEventListener('mousedown', e => { e.preventDefault(); acSelect(i); });
                acEl.appendChild(item);
            });
            acEl.insertAdjacentHTML('beforeend',
                '<div class="map-ac-attr">'
                + '© <a href="https://www.openstreetmap.org/copyright" target="_blank" tabindex="-1">OpenStreetMap</a>'
                + '</div>');
        }

        acEl.removeAttribute('hidden');
        searchInput?.setAttribute('aria-expanded', 'true');
    }

    function acHide() {
        acEl.setAttribute('hidden', '');
        searchInput?.setAttribute('aria-expanded', 'false');
        _acFocusIdx  = -1;
        _acLastQuery = ''; /* reset para permitir re-buscar la misma query */
    }

    function acSetFocus(idx) {
        const items = acEl.querySelectorAll('.map-ac-item');
        items.forEach(el => el.classList.remove('is-focused'));
        _acFocusIdx = Math.max(0, Math.min(items.length - 1, idx));
        items[_acFocusIdx]?.classList.add('is-focused');
        items[_acFocusIdx]?.scrollIntoView({ block: 'nearest' });
    }

    /* ─── Seleccionar sugerencia ───────────────────────────── */
    function acSelect(idx) {
        const r = _acResults[idx];
        if (!r) return;

        const lat = parseFloat(r.lat);
        const lng = parseFloat(r.lon);

        /* Actualizar texto del input al nombre corto */
        if (searchInput) {
            searchInput.value = r.display_name.split(',').slice(0, 2).join(',').trim();
        }
        acHide();

        /* Centrar mapa + marcador + coordenadas */
        if (!mapReady) {
            initMap();
            setTimeout(() => {
                placeMarker(lat, lng);
                mapObj?.setView([lat, lng], 17);
                scheduleReverseGeocode(lat, lng);
            }, 400);
        } else {
            placeMarker(lat, lng);
            mapObj.setView([lat, lng], 17);
            scheduleReverseGeocode(lat, lng);
        }
    }

    /* ─── Consulta a Nominatim con debounce + AbortController ─ */
    async function acFetch(query) {
        if (query.length < NOM.minChars) { acHide(); return; }
        if (query === _acLastQuery) return;   /* evitar llamada duplicada */
        _acLastQuery = query;

        clearTimeout(_acTimer);
        if (_acController) { _acController.abort(); _acController = null; }

        _acTimer = setTimeout(async () => {
            _acController = new AbortController();
            try {
                const params = new URLSearchParams({
                    q:              query,
                    format:         'json',
                    limit:          NOM.limit,
                    countrycodes:   NOM.country,
                    addressdetails: 1,
                    viewbox:        NOM.viewbox,
                    bounded:        NOM.bounded,
                });
                const res = await fetch(`${NOM.base}/search?${params}`, {
                    signal:  _acController.signal,
                    headers: { 'Accept-Language': 'es' },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                acShow(await res.json());
            } catch (err) {
                if (err.name !== 'AbortError') acHide();
            } finally {
                _acController = null;
            }
        }, NOM.debounceMs);
    }

    /* ─── Búsqueda directa (botón / Enter sin selección) ──── */
    async function searchAddress(query) {
        if (!query.trim()) return;
        acHide();
        const btn = document.getElementById('btnMapSearch');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Buscando…'; }
        try {
            const params = new URLSearchParams({
                q:            query,
                format:       'json',
                limit:        1,
                countrycodes: NOM.country,
            });
            const res  = await fetch(`${NOM.base}/search?${params}`, { headers: { 'Accept-Language': 'es' } });
            const data = await res.json();
            if (data.length > 0) {
                _acResults = data;
                acSelect(0);
            } else {
                /* Mostrar "sin resultados" en el dropdown en lugar de alert */
                acShow([]);
            }
        } catch (err) {
            acShow([]);
        } finally {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-search"></i> Buscar'; }
        }
    }

    /* ─── Eventos del input ────────────────────────────────── */
    searchInput?.addEventListener('input', e => {
        const q = e.target.value.trim();
        if (!q) { acHide(); return; }
        acFetch(q);
    });

    searchInput?.addEventListener('keydown', e => {
        const open = !acEl.hasAttribute('hidden');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!open && searchInput.value.trim().length >= NOM.minChars) {
                acFetch(searchInput.value.trim());
            } else {
                acSetFocus(_acFocusIdx + 1);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            acSetFocus(_acFocusIdx - 1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (open && _acFocusIdx >= 0) {
                acSelect(_acFocusIdx);
            } else {
                searchAddress(searchInput.value);
            }
        } else if (e.key === 'Escape') {
            acHide();
        }
    });

    searchInput?.addEventListener('blur', () => {
        /* Delay para que mousedown en item se procese antes del cierre */
        setTimeout(acHide, 150);
    });

    document.getElementById('btnMapSearch')?.addEventListener('click', () => {
        searchAddress(searchInput?.value ?? '');
    });

    /* Cerrar al hacer clic fuera del buscador */
    document.addEventListener('click', e => {
        if (!e.target.closest('.map-search-wrap')) acHide();
    });

    /* ─── Utilidad ─────────────────────────────────────────── */
    function escHtml(str) {
        return str.replace(/[&<>"']/g, c =>
            ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
    }

})();
</script>
@endpush
