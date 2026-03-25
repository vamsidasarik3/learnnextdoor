/**
 * Class Next Door — Push Notification Manager
 * ═══════════════════════════════════════════════════════════════
 * Responsibilities:
 *   1. Register /sw.js service worker
 *   2. After a booking is confirmed (phone OTP verified), prompt
 *      the user to enable push notifications
 *   3. Subscribe using the VAPID public key from the page
 *   4. POST the subscription to /api/push/subscribe
 *   5. Handle unsubscription
 *
 * This file is loaded from base.php via <script src="push.js">.
 * It reads:
 *   - window.CND_PUSH_PUBLIC_KEY  — VAPID public key (set in base.php)
 *   - window.CND_BASE_URL         — base URL for API calls
 *   - window.CND_CSRF             — { name, token } for AJAX
 */

(function (w, d) {
    'use strict';

    // ── Feature detection ──────────────────────────────────────────────
    if (!('serviceWorker' in navigator) || !('PushManager' in w)) {
        // Push API unavailable (HTTP or old browser) — silently skip
        return;
    }

    var PUBLIC_KEY = w.CND_PUSH_PUBLIC_KEY || '';
    var BASE_URL = (w.CND_BASE_URL || '').replace(/\/$/, '') + '/';
    var CSRF = w.CND_CSRF || {};

    // ── Register Service Worker ────────────────────────────────────────
    var swReg = null;
    navigator.serviceWorker.register('/sw.js', { scope: '/' })
        .then(function (reg) {
            swReg = reg;
            console.info('[CND Push] SW registered, scope:', reg.scope);

            // Listen for subscription refresh messages from the SW
            navigator.serviceWorker.addEventListener('message', function (e) {
                if (e.data && e.data.type === 'CND_PUSH_REFRESHED') {
                    var phone = localStorage.getItem('cnd_push_phone') || '';
                    if (phone && e.data.subscription) {
                        postSubscription(phone, e.data.subscription);
                    }
                }
            });
        })
        .catch(function (err) {
            console.warn('[CND Push] SW registration failed:', err);
        });

    // ── Public API ─────────────────────────────────────────────────────

    /**
     * Call this after booking confirmation (when we have the verified phone).
     * Shows the browser's permission prompt (if not already granted),
     * then subscribes and sends the subscription to the server.
     *
     * @param {string} phone  10-digit verified phone
     */
    w.cndRequestPush = function (phone) {
        if (!swReg || !PUBLIC_KEY || !phone) return;

        Notification.requestPermission().then(function (permission) {
            if (permission !== 'granted') {
                console.info('[CND Push] Permission denied by user.');
                return;
            }

            var appServerKey = urlBase64ToUint8Array(PUBLIC_KEY);

            swReg.pushManager.getSubscription().then(function (existingSub) {
                // Already subscribed — just re-post in case the key changed
                if (existingSub) {
                    return postSubscription(phone, existingSub.toJSON());
                }

                return swReg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: appServerKey,
                });
            }).then(function (sub) {
                if (sub) {
                    localStorage.setItem('cnd_push_phone', phone);
                    postSubscription(phone, sub.toJSON ? sub.toJSON() : sub);
                }
            }).catch(function (err) {
                console.warn('[CND Push] Subscribe error:', err);
            });
        });
    };

    /**
     * Unsubscribe from push notifications for a given phone.
     */
    w.cndRevokePush = function (phone) {
        if (!swReg) return;
        swReg.pushManager.getSubscription().then(function (sub) {
            if (sub) sub.unsubscribe();
        });
        localStorage.removeItem('cnd_push_phone');
        fetchJson(BASE_URL + 'api/push/unsubscribe', { phone: phone });
    };

    // ── Internal helpers ──────────────────────────────────────────────

    function postSubscription(phone, subJson) {
        fetchJson(BASE_URL + 'api/push/subscribe', {
            phone: phone,
            subscription: subJson,
        }).then(function (res) {
            if (res && res.success) {
                console.info('[CND Push] Subscription saved for phone', phone.slice(0, 4) + '****');
            } else {
                console.warn('[CND Push] Server rejected subscription:', res && res.message);
            }
        });
    }

    function fetchJson(url, body) {
        var payload = Object.assign({}, body);
        if (CSRF.name) payload[CSRF.name] = CSRF.token;
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })
            .then(function (r) { return r.json(); })
            .catch(function (e) { console.warn('[CND Push] Fetch error:', e); return null; });
    }

    /** Convert a VAPID public key from Base64url to Uint8Array. */
    function urlBase64ToUint8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var rawData = atob(base64);
        var buffer = new Uint8Array(rawData.length);
        for (var i = 0; i < rawData.length; i++) {
            buffer[i] = rawData.charCodeAt(i);
        }
        return buffer;
    }

}(window, document));
