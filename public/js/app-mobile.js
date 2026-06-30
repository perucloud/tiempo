if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/app/service-worker.js', {
            scope: '/app/',
        }).catch(() => {
            // La PWA debe seguir funcionando aunque el registro falle en desarrollo local.
        });
    });
}
