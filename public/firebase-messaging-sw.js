// Firebase Cloud Messaging Service Worker for background notifications
// This file MUST be at the root of the site (/firebase-messaging-sw.js)
// v3 — status-specific tags, notifications stack instead of replacing

importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

if (!firebase.apps.length) {
    firebase.initializeApp({
        apiKey:            'AIzaSyBFamccFovToRjeYRCkXuHOTcdsvogFYiE',
        authDomain:        'project-vnf.firebaseapp.com',
        projectId:         'project-vnf',
        storageBucket:     'project-vnf.firebasestorage.app',
        messagingSenderId: '341085169489',
        appId:             '1:341085169489:web:25a41d4e076f614decd570',
    });
}

const messaging = firebase.messaging();

// Handle background messages here and show exactly ONE notification.
// We use a data-only FCM payload so Chrome's push infrastructure never
// auto-displays anything — this handler is the single source of truth.
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
