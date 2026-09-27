const CACHE_NAME = 'e-belajar-v1.0.0';

// Relative assets based on sw location
const PRECACHE_ASSETS = [
    './',
    'offline.html',
    'manifest.json',
    'favicon.ico',
    'icons/icon-72x72.png',
    'icons/icon-96x96.png',
    'icons/icon-128x128.png',
    'icons/icon-144x144.png',
    'icons/icon-152x152.png',
    'icons/icon-192x192.png',
    'icons/icon-384x384.png',
    'icons/icon-512x512.png',
    'icons/maskable-icon-512x512.png'
];

// Helper to get offline URL
function getOfflineUrl() {
    return new URL('offline.html', self.location).href;
}

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                const urlsToCache = PRECACHE_ASSETS.map((asset) => new URL(asset, self.location).href);
                return cache.addAll(urlsToCache).catch((err) => {
                    console.warn('[PWA SW] Precache partial error:', err);
                });
            })
            .then(() => self.skipWaiting())
    );
});

// Activate Event - Clean up old cache versions
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        console.log('[PWA SW] Removing old cache version:', name);
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Only process GET requests (POST, PUT, DELETE should never be cached)
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // External font / cdn caching
    if (url.origin !== self.origin) {
        if (url.hostname.includes('fonts.googleapis.com') ||
            url.hostname.includes('fonts.gstatic.com') ||
            url.hostname.includes('cdn.tailwindcss.com') ||
            url.hostname.includes('cdnjs.cloudflare.com')) {
            event.respondWith(
                caches.match(request).then((cachedResponse) => {
                    const fetchPromise = fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            const responseToCache = networkResponse.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(request, responseToCache);
                            });
                        }
                        return networkResponse;
                    }).catch(() => cachedResponse);
                    return cachedResponse || fetchPromise;
                })
            );
        }
        return;
    }

    // HTML Navigation requests - Network-first with offline fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    const offlinePage = await caches.match(getOfflineUrl());
                    return offlinePage || caches.match('offline.html');
                })
        );
        return;
    }

    // Static assets (CSS, JS, Images, Icons) - Cache-first with background revalidation
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font' ||
        url.pathname.includes('/icons/') ||
        url.pathname.includes('/build/') ||
        url.pathname.includes('/storage/')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                        }
                    }).catch(() => {});
                    return cachedResponse;
                }

                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Default fetch
    event.respondWith(
        caches.match(request).then((response) => response || fetch(request))
    );
});
