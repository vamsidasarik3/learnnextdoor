/**
 * Class Next Door — Service Worker
 * ═══════════════════════════════════════════════════════════════
 * Responsibilities:
 *   1. Web Push — receive and display push notifications
 *   2. Notification click — navigate to the booking/action URL
 *   3. Basic offline fallback (optional, for network-first caching)
 *
 * Registration:  see /assets/frontend/js/push.js
 * Scope:         / (root — must be served from root)
 * File location: /public/sw.js  (accessible at example.com/sw.js)
 */

const CACHE_NAME = 'cnd-v1';

// ── Install ──────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    self.skipWaiting(); // Activate immediately without waiting
});

// ── Activate ─────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

// ── Push event ───────────────────────────────────────────────────────
self.addEventListener('push', event => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'Class Next Door', body: event.data ? event.data.text() : 'You have a notification.' };
    }

    const title = data.title || 'Class Next Door';
    const options = {
        body: data.body || '',
        icon: data.icon || '/assets/frontend/img/icon-192.png',
        badge: data.badge || '/assets/frontend/img/icon-72.png',
        tag: data.tag || 'cnd-notification',
        data: { url: data.url || '/' },
        vibrate: data.vibrate || [200, 100, 200],
        requireInteraction: data.requireInteraction !== false,
        actions: [
            { action: 'view', title: 'View Details' },
            { action: 'dismiss', title: 'Dismiss' },
        ],
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// ── Notification click ────────────────────────────────────────────────
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const targetUrl = (event.notification.data && event.notification.data.url)
        ? event.notification.data.url
        : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            // If we already have a window open, focus it and navigate
            for (const client of windowClients) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise open a new window
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// ── Push subscription change ──────────────────────────────────────────
// Fired when the browser internally refreshes the push subscription
self.addEventListener('pushsubscriptionchange', event => {
    event.waitUntil(
        self.registration.pushManager.subscribe({ userVisibleOnly: true })
            .then(newSub => {
                // The frontend will re-subscribe when it next loads
                // Post message to clients so they can re-register
                return self.clients.matchAll().then(clients => {
                    clients.forEach(client => {
                        client.postMessage({ type: 'CND_PUSH_REFRESHED', subscription: newSub.toJSON() });
                    });
                });
            })
    );
});
