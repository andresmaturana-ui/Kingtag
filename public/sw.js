// Service worker mínimo: guarda los archivos fijos para que la app abra
// rápido. Las páginas y los datos siempre se piden a la red.
const CACHE = 'kingtag-v8';
const ASSETS = ['/css/app.css', '/js/app.js', '/js/map.js', '/fonts/permanent-marker.woff2', '/fonts/bebas-neue.woff2', '/icons/icon-192.png', '/img/logo.svg', '/manifest.webmanifest'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(ASSETS)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))),
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET' || url.origin !== location.origin || !ASSETS.includes(url.pathname)) {
        return;
    }
    // Primero la red, para no quedarse con versiones viejas; el caché si no hay señal.
    event.respondWith(
        fetch(event.request)
            .then((res) => {
                const copy = res.clone();
                caches.open(CACHE).then((cache) => cache.put(url.pathname, copy));
                return res;
            })
            .catch(() => caches.match(url.pathname)),
    );
});
