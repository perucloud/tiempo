/**
 * TIEMPO — Tracking GPS del repartidor
 * watchPosition() + POST a /api/v1/repartidores/ubicacion cada 10s
 * El sistema bloquea inicio de turno si no hay GPS disponible.
 */
(function () {
    const API_URL        = '/api/v1/repartidores/ubicacion';
    const INTERVAL_MS    = 10000;
    const REPARTIDOR_ID  = window.TIEMPO_REPARTIDOR_ID ?? null;

    const btnIniciar   = document.getElementById('btn-iniciar-turno');
    const btnTerminar  = document.getElementById('btn-terminar-turno');
    const statusEl     = document.getElementById('gps-status');
    const posicionEl   = document.getElementById('gps-posicion');

    if (!btnIniciar) return;

    let watchId  = null;
    let interval = null;
    let lastPos  = null;

    function setStatus(msg, tipo) {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.className = 'gps-status-badge gps-status--' + (tipo ?? 'neutral');
    }

    function setPosition(lat, lng) {
        lastPos = { lat, lng };
        if (posicionEl) posicionEl.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    }

    function sendLocation() {
        if (!lastPos || !REPARTIDOR_ID) return;

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                repartidor_id: REPARTIDOR_ID,
                latitud:       lastPos.lat,
                longitud:      lastPos.lng,
            }),
        }).catch(() => {
            setStatus('Error de conexion — reintentando...', 'warn');
        });
    }

    function iniciarTracking() {
        if (!navigator.geolocation) {
            setStatus('GPS no disponible en este dispositivo.', 'error');
            btnIniciar.disabled = false;
            return;
        }

        setStatus('Activando GPS...', 'neutral');

        watchId = navigator.geolocation.watchPosition(
            function (pos) {
                setPosition(pos.coords.latitude, pos.coords.longitude);
                setStatus('GPS activo — enviando posicion', 'active');
            },
            function (err) {
                const msgs = {
                    1: 'Permiso de GPS denegado. Activa el GPS para trabajar.',
                    2: 'No se pudo obtener la ubicacion.',
                    3: 'Sin senal GPS. Muevete a un lugar con mejor cobertura.',
                };
                setStatus(msgs[err.code] ?? 'Error de GPS.', 'error');
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 5000 },
        );

        interval = setInterval(sendLocation, INTERVAL_MS);

        btnIniciar.classList.add('hidden');
        if (btnTerminar) btnTerminar.classList.remove('hidden');
    }

    function terminarTracking() {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        if (interval !== null) {
            clearInterval(interval);
            interval = null;
        }
        lastPos = null;
        setStatus('Turno terminado — GPS detenido', 'neutral');
        if (posicionEl) posicionEl.textContent = '—';

        btnIniciar.classList.remove('hidden');
        if (btnTerminar) btnTerminar.classList.add('hidden');
    }

    btnIniciar.addEventListener('click', function () {
        btnIniciar.disabled = true;
        iniciarTracking();
    });

    if (btnTerminar) {
        btnTerminar.addEventListener('click', terminarTracking);
    }

    /* Detener tracking si el repartidor cierra o recarga la pestaña */
    window.addEventListener('beforeunload', function () {
        if (watchId !== null) navigator.geolocation.clearWatch(watchId);
        if (interval !== null) clearInterval(interval);
    });
})();
