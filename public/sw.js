const CACHE_NAME = 'piramid-pwa-v4';
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.json',
  '/logo.ico',
  '/images/icon-192x192.png',
  '/images/icon-512x512.png',
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

// Fetch Event (Network First dengan fallback offline.html untuk navigasi)
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
          if (cachedResponse) {
            return cachedResponse;
          }
          if (event.request.mode === 'navigate' || event.request.headers.get('accept')?.includes('text/html')) {
            return caches.match('/offline.html');
          }
          return null;
        });
      })
  );
});

// ================= PUSH NOTIFICATIONS EVENT LISTENER =================
self.addEventListener('push', (event) => {
  let data = {
    title: 'Piramid Qurban & Aqiqah',
    body: 'Ada pembaruan status pesanan atau informasi terbaru untuk Anda.',
    icon: '/images/icon-192x192.png',
    badge: '/images/icon-72x72.png',
    url: '/transaksi',
  };

  if (event.data) {
    try {
      const payload = event.data.json();
      data = Object.assign(data, payload);
    } catch (e) {
      data.body = event.data.text();
    }
  }

  const options = {
    body: data.body,
    icon: data.icon || '/images/icon-192x192.png',
    badge: data.badge || '/images/icon-72x72.png',
    vibrate: [100, 50, 100, 50, 150],
    data: {
      url: data.url || '/transaksi',
      dateOfArrival: Date.now(),
    },
    actions: [
      {
        action: 'open_url',
        title: 'Lihat Detail',
      },
    ],
  };

  event.waitUntil(self.registration.showNotification(data.title, options));
});

// Notification Click Event (Buka URL tujuan saat notifikasi di smartphone diklik)
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  const targetUrl = event.notification.data?.url || '/transaksi';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Jika tab/aplikasi sudah terbuka, fokuskan
      for (const client of clientList) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      // Jika belum terbuka, luncurkan jendela baru
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
