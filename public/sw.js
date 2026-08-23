const CACHE_NAME = 'piramid-pwa-v1';
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/logo.ico',
  '/images/logo.png',
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('PWA: Gagal meng-cache beberapa aset statis saat instalasi', err);
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

// Fetch Event (Network First with Cache Fallback for navigation, Cache First for static images)
self.addEventListener('fetch', (event) => {
  // Hanya proses request GET
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

  // Untuk request HTML / Halaman: Network First
  if (event.request.mode === 'navigate' || event.request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match(event.request).then((res) => res || caches.match('/'));
      })
    );
    return;
  }

  // Untuk file statis / gambar: Cache First -> Network Fallback
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(event.request).then((networkResponse) => {
        if (
          networkResponse &&
          networkResponse.status === 200 &&
          (url.pathname.startsWith('/images') || url.pathname.endsWith('.png') || url.pathname.endsWith('.jpg') || url.pathname.endsWith('.ico'))
        ) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      });
    })
  );
});
