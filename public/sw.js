const CACHE_NAME = 'piramid-pwa-v2';
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/logo.ico',
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('PWA: Cache install notice', err);
      });
    })
  );
  self.skipWaiting();
});

// Activate Event (Bersihkan Cache Versi Lama)
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch Event (Network First untuk aset dan halaman)
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // Abaikan request API/Webhook/Midtrans snap dan third-party scripts
  if (
    url.pathname.startsWith('/api') ||
    url.pathname.startsWith('/webhooks') ||
    url.pathname.startsWith('/pembayaran') ||
    url.origin.includes('midtrans')
  ) {
    return;
  }

  // Network First strategy
  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        if (
          networkResponse &&
          networkResponse.status === 200 &&
          (url.pathname.startsWith('/images') ||
            url.pathname.startsWith('/assets') ||
            url.pathname.endsWith('.png') ||
            url.pathname.endsWith('.jpg') ||
            url.pathname.endsWith('.jpeg') ||
            url.pathname.endsWith('.ico') ||
            url.pathname.endsWith('.svg'))
        ) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        return caches.match(event.request).then((cachedResponse) => {
          return cachedResponse || (event.request.mode === 'navigate' ? caches.match('/') : null);
        });
      })
  );
});
