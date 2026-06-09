const CACHE = 'v1';
const FILES = ['/', '/index.html', '/bootstrap.min.css', '/app.png'];

self.addEventListener('install', e =>
  e.waitUntil(caches.open(CACHE).then(c => c.addAll(FILES)))
);

self.addEventListener('fetch', e =>
  e.respondWith(
    fetch(e.request)
      .then(r => { caches.open(CACHE).then(c => c.put(e.request, r.clone())); return r; })
      .catch(() => caches.match(e.request))
  )
);
