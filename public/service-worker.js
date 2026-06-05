const CACHE_NAME = 'bukit-foto-v2';
const urlsToCache = [
  '/favicon.ico',
  '/bukitfoto.png'
];

// Install event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache opened');
        return cache.addAll(urlsToCache);
      })
      .catch(error => console.log('Cache error:', error))
  );
  self.skipWaiting();
});

// Fetch event
self.addEventListener('fetch', event => {
  // Skip cross-origin requests and non-GET requests
  if (!event.request.url.startsWith(self.location.origin) || event.request.method !== 'GET') {
    return;
  }

  // Handle navigation requests (HTML pages) directly from network
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match('/favicon.ico'); // Just a fallback
      })
    );
    return;
  }

  // For static assets, try cache first, then network
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request).then(response => {
          // Only cache valid 200 responses that are not redirected
          if (!response || response.status !== 200 || response.type !== 'basic' || response.redirected) {
            return response;
          }
          const clonedResponse = response.clone();
          caches.open(CACHE_NAME)
            .then(cache => {
              cache.put(event.request, clonedResponse);
            });
          return response;
        }).catch(() => null);
      })
  );
});

// Activate event
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});
