const CACHE_NAME = 'tiempo-app-shell-v3';
const STATIC_ASSETS = [
    '/app/manifest.webmanifest',
    '/css/app-mobile.css',
    '/js/app-mobile.js',
    '/js/push-notifications.js',
    '/app/icon.svg',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .finally(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys
                .filter((key) => key !== CACHE_NAME)
                .map((key) => caches.delete(key)),
        )),
    );

    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);
    const isStaticAsset = STATIC_ASSETS.includes(url.pathname);

    // No cachear datos de clientes, pedidos, pagos ni sesiones sin una estrategia explicita.
    if (! isStaticAsset || event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => cached || fetch(event.request)),
    );
});

self.addEventListener('push', (event) => {
    const payload = event.data ? event.data.json() : {};
    event.waitUntil(self.registration.showNotification(payload.title || 'TIEMPO Delivery', {
        body: payload.body || 'Tienes una actualización.', icon: '/app/icon.svg',
        badge: '/app/icon.svg', data: payload.data || {},
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data?.url || '/app'));
});
