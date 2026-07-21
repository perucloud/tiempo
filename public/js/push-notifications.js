(function () {
    const button = document.getElementById('push-enable');
    const publicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
    const subscribeUrl = document.querySelector('meta[name="push-subscribe-url"]')?.content;
    const workerUrl = document.querySelector('meta[name="push-worker-url"]')?.content;
    if (!button) return;

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !publicKey) {
        button.hidden = true;
        return;
    }

    const decodeKey = value => {
        const padding = '='.repeat((4 - value.length % 4) % 4);
        const binary = atob((value + padding).replace(/-/g, '+').replace(/_/g, '/'));
        return Uint8Array.from([...binary].map(char => char.charCodeAt(0)));
    };

    button.addEventListener('click', async () => {
        button.disabled = true;
        button.textContent = 'Activando...';
        try {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') throw new Error('Permiso de notificaciones denegado.');
            const registration = await navigator.serviceWorker.register(workerUrl);
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true, applicationServerKey: decodeKey(publicKey),
            });
            const response = await fetch(subscribeUrl, {
                method: 'POST', headers: {
                    'Content-Type': 'application/json', 'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                }, body: JSON.stringify(subscription.toJSON()),
            });
            if (!response.ok) throw new Error('Verifica tu acceso antes de activar notificaciones.');
            button.textContent = 'Notificaciones activadas';
        } catch (error) {
            button.textContent = error.message;
            button.disabled = false;
        }
    });
})();
