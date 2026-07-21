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
    const quoteStatus = document.getElementById('delivery-quote-status');
    const deliveryPrice = document.getElementById('delivery-price');
    const orderTotal = document.getElementById('order-total');
    const submitButton = document.getElementById('order-submit');

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
            async function (position) {
                const lat = position.coords.latitude.toFixed(7);
                const lng = position.coords.longitude.toFixed(7);

                latIn.value = lat;
                lngIn.value = lng;

                label.textContent = 'Ubicación capturada';
                hint.textContent  = 'Validando cobertura y calculando tarifa...';
                btn.classList.remove('geo-btn--loading');
                btn.classList.add('geo-btn--success');
                btn.disabled = false;

                try {
                    const response = await fetch('/app/delivery/quote', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        },
                        body: JSON.stringify({ latitud: lat, longitud: lng }),
                    });
                    const quote = await response.json();

                    if (!response.ok || !quote.available) {
                        throw new Error(quote.message || 'No hay cobertura para esta ubicación.');
                    }

                    deliveryPrice.textContent = `S/ ${quote.final_delivery_price}`;
                    orderTotal.textContent = `S/ ${quote.order_total}`;
                    quoteStatus.textContent = `${quote.zone} · ${quote.distance_km} km · ~${Math.round(quote.estimated_total_minutes)} min`;
                    hint.textContent = 'Cobertura confirmada para tu ubicación.';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Crear pedido →';
                } catch (error) {
                    quoteStatus.textContent = error.message;
                    hint.textContent = 'Prueba con otra ubicación dentro de cobertura.';
                    submitButton.disabled = true;
                    submitButton.textContent = 'Ubicación fuera de cobertura';
                    btn.classList.remove('geo-btn--success');
                    btn.classList.add('geo-btn--error');
                }
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
                if (submitButton) submitButton.disabled = true;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 },
        );
    });
})();
