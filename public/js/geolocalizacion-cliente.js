/**
 * TIEMPO — Geolocalización del cliente
 * Captura puntual de lat/lng al confirmar pedido en /app
 */
(function () {
    const btn    = document.getElementById('geo-btn');
    const label  = document.getElementById('geo-label');
    const hint   = document.getElementById('geo-hint');
    const latIn  = document.getElementById('geo-lat');
    const lngIn  = document.getElementById('geo-lng');

    if (!btn) return;

    if (!navigator.geolocation) {
        btn.disabled = true;
        label.textContent = 'GPS no disponible en este dispositivo';
        hint.textContent  = 'Solo se usara la direccion de texto.';
        btn.classList.add('geo-btn--unavailable');
        return;
    }

    btn.addEventListener('click', function () {
        btn.disabled = true;
        label.textContent = 'Obteniendo ubicacion...';
        hint.textContent  = 'Autoriza el acceso a tu GPS cuando el navegador lo pida.';
        btn.classList.add('geo-btn--loading');

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude.toFixed(7);
                const lng = position.coords.longitude.toFixed(7);

                latIn.value = lat;
                lngIn.value = lng;

                label.textContent = '✓ Ubicacion capturada';
                hint.textContent  = 'El repartidor podrá encontrarte con exactitud.';
                btn.classList.remove('geo-btn--loading');
                btn.classList.add('geo-btn--success');
                btn.disabled = false;
            },
            function (error) {
                const mensajes = {
                    1: 'Permiso de GPS denegado. Solo se usara la direccion de texto.',
                    2: 'No se pudo obtener la ubicacion. Verifica tu GPS.',
                    3: 'Tiempo de espera agotado. Intenta de nuevo.',
                };
                label.textContent = 'No se pudo obtener la ubicacion';
                hint.textContent  = mensajes[error.code] ?? 'Error desconocido. El pedido continuara sin GPS.';
                btn.classList.remove('geo-btn--loading');
                btn.classList.add('geo-btn--error');
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 },
        );
    });
})();
