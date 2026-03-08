// Firebase Cloud Messaging Service Worker
// Served dynamically by Laravel — Firebase config is injected from .env, never hardcoded in git.
// v3 — status-specific tags, notifications stack instead of replacing

importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

if (!firebase.apps.length) {
    firebase.initializeApp({
        apiKey:            '{{ config('services.firebase.api_key') }}',
        authDomain:        '{{ config('services.firebase.auth_domain') }}',
        projectId:         '{{ config('services.firebase.project_id') }}',
        storageBucket:     '{{ config('services.firebase.storage_bucket') }}',
        messagingSenderId: '{{ config('services.firebase.messaging_sender_id') }}',
        appId:             '{{ config('services.firebase.app_id') }}',
    });
}

const messaging = firebase.messaging();

// Handle background messages and show exactly ONE notification.
// Data-only payload means Chrome never auto-displays anything.
messaging.onBackgroundMessage(function (payload) {
    const data  = payload.data || {};
    const title = data.title || 'V&F Ice Plant';
    const body  = data.body  || '';
    const tag   = data.tag   || 'vnf-order';
    const link  = data.link  || '/customer/orders';
    const icon  = data.icon  || '/images/logo.png';

    self.registration.showNotification(title, {
        body:               body,
        tag:                tag,
        icon:               icon,
        requireInteraction: true,
        data:               { link: link },
    });
});

// Handle notification click — open the orders page
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.link || '/customer/orders';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.focus();
                    client.navigate(url);
                    return;
                }
            }
            return clients.openWindow(url);
        })
    );
});
