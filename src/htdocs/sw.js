importScripts('/public/js/cache-polyfill.js');

var staticFiles = [
  "/public/js/cache-polyfill.js",
  "/public/js/main.js?v=15",
  "/public/js/graph.js?v=15",
  "/public/js/vendor.min.js",
  "/public/css/critical.css",
  "/public/css/style.css?v=15",
  "/public/css/vendor.min.css",
  "/public/css/themes/darkly.min.css",
  "/public/css/themes/default.min.css",
  "/public/img/dragon-192.png",
  "/public/img/dragon-512.png",
  "/public/img/dragon.png",
  "/public/img/flags/en.png",
  "/public/img/flags/pl.png",
  "/public/fonts/fontawesome-webfont.woff2?v=4.7.0",
  "/public/fonts/weathericons-regular-webfont.woff",
  "/offline",
  "/",
  "/sensors?avg_type=1",
  "/sensors?avg_type=24",
  "/graph_data.json?type=pm&range=day&ma_h=1",
  "/graph_data.json?type=pm&range=week&ma_h=24",
];

var dynamicResources = [
  "/sensors",
  "/graph_data.json"
];

var unavailableOffline = [
  "/graphs",
  "/about",
  "/debug"
];

self.addEventListener('install', function(e) {
  e.waitUntil(
    caches.open('airqualityinfo').then(function(cache) {
      return cache.addAll(staticFiles);
    })
  );
});

self.addEventListener('fetch', function(event) {
    function endsWithAny(subject, suffixes) {
      return suffixes.some(function(suffix) {
        return subject.endsWith(suffix);
      });
    }

    var url = new URL(event.request.url);
    var response;
    if (dynamicResources.includes(url.pathname)) { // fetch it from network and update the cache. in offline - get from the cache.
      response = fetch(event.request).then(function(response) {
          return caches.open('airqualityinfo').then(function(cache) {
              cache.put(event.request, response.clone());
              return response;
          });
      }).catch(function() {
          return caches.match(event.request).then(function(response) {
              return response || caches.match("/offline");
          });
      });
    } else if (staticFiles.includes(url.pathname + url.search)) { // return static files from cache, then from network
        response = caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        });
    } else { // return from network or fallback to /offline
        response = fetch(event.request).catch(function() {
            if (endsWithAny(url.pathname, unavailableOffline)) {
                return caches.match("/offline");
            } else {
                return this;
            }
        });
    }
    event.respondWith(response);
});