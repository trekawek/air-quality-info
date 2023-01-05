class AqiClusterer extends markerClusterer.MarkerClusterer {
    renderClusters() {
        // generate stats to pass to renderers
        const stats = new markerClusterer.ClusterStats(this.markers, this.clusters);
        const map = this.getMap();
    
        this.clusters.forEach((cluster) => {
          if (cluster.markers.length === 1) {
            cluster.marker = cluster.markers[0];
          } else {
            cluster.level = Math.round(
                cluster.markers
                    .map(m => m._level)
                    .reduce((a, b) => a + b, 0)
                / cluster.markers.length);
            cluster.marker = this.renderer.render(cluster, stats);
    
            if (this.onClusterClick) {
              cluster.marker.addListener(
                "click",
                /* istanbul ignore next */
                (event) => {
                  google.maps.event.trigger(
                    this,
                    markerClusterer.MarkerClustererEvents.CLUSTER_CLICK,
                    cluster
                  );
                  this.onClusterClick(event, cluster, map);
                }
              );
            }
          }
    
          cluster.marker.setMap(map);
        });
      }
}

var mapsLoaded = (function() {

const COLORS = [
    "#57b108",
    "#b0dd10",
    "#ffd911",
    "#e58100",
    "#990000"
];

var windows = [];

function mapsLoaded() {
    var mapDiv = document.getElementById('map');
    var request = new XMLHttpRequest();
    request.open('GET', mapDiv.dataset.url, true);
    request.onload = function() {
        if (request.status == 200) {
            var data = JSON.parse(request.responseText);
            initMap(mapDiv, data);
        }
    };
    request.send();
}

function getPosition(sensor) {
    return {
        lat: Number(sensor.lat),
        lng: Number(sensor.lng)
    };
}

function removeWindow(infoWindow) {
    var index = windows.indexOf(infoWindow);
    if (index != -1) {
        windows.splice(index, 1);
    }
}

function createInfoWindow(sensor, map, position) {
    var request = new XMLHttpRequest();
    request.open('GET', sensor.info_path, true);
    request.onload = function() {
        if (request.status == 200) {
            var infoWindow = new google.maps.InfoWindow({
                content: request.responseText,
                position: position
            })
            infoWindow.open(map);
            infoWindow.addListener('closeclick', e => removeWindow(infoWindow));
            windows.forEach(w => w.close());
            windows = [];
            windows.push(infoWindow);
        }
    };
    request.send();
}

function addCircle(sensor, position, map) {
    var circle = new google.maps.Circle({
            strokeColor: COLORS[sensor.averages.max_level],
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: COLORS[sensor.averages.max_level],
            fillOpacity: 0.35,
        center: position,
        radius: sensor.radius
    });
    circle.addListener('click', function() {
        createInfoWindow(sensor, map, position);
    });
    circle._radius = sensor.radius;
    circle._level = sensor.averages.max_level;

    var latLng = new google.maps.LatLng(position);
    circle.getPosition = function() {
        return latLng;
    };
    return circle;
}

function clusterRender({ count, position, level }, stats) {
    const color = COLORS[level];
    const svg = window.btoa(`
<svg fill="${color}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240">
<circle cx="120" cy="120" opacity=".6" r="70" />
<circle cx="120" cy="120" opacity=".3" r="90" />
<circle cx="120" cy="120" opacity=".2" r="110" />
</svg>`);
    return new google.maps.Marker({
        position,
        icon: {
            url: `data:image/svg+xml;base64,${svg}`,
            scaledSize: new google.maps.Size(45, 45),
        },
        label: {
            text: String(count),
            color: "rgba(255,255,255,0.9)",
            fontSize: "12px",
        },
        zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count,
    });
}

function initMap(mapDiv, data) {
    if (data.length == 0) {
        return;
    }

    var styledMapTypeDark = new google.maps.StyledMapType(
        [
            {
                "featureType": "all",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#242f3e"
                    }
                ]
            },
            {
                "featureType": "all",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#746855"
                    }
                ]
            },
            {
                "featureType": "all",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#242f3e"
                    }
                ]
            },
            {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "poi.attraction",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.business",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#263c3f"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#6b9a76"
                    }
                ]
            },
            {
                "featureType": "poi.school",
                "elementType": "labels.text",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "poi.school",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#38414e"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#212a37"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9ca5b3"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#746855"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#1f2835"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#f3d19c"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#2f3948"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#17263c"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#515c6d"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#17263c"
                    }
                ]
            }
        ],
        {
        name: __('Night view')
      }
    );

    let styledMapTypeLight = new google.maps.StyledMapType(
        [
            {
                "featureType": "all",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": "-36"
                    },
                    {
                        "lightness": "0"
                    },
                    {
                        "gamma": "1.01"
                    }
                ]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            }
        ],
        {
            name: __('Map')
          }
    )

    var initPosition = getPosition(data[0]);
    
    var map = new google.maps.Map(mapDiv, {
        zoom: 15,
        mapTypeControlOptions: {
            mapTypeIds: ['light_map', 'satellite', 'dark_map', ]
        },
        center: initPosition,
        streetViewControl: false,
        disableDefaultUI: mapDiv.dataset.hideControls
    });

    //Associate the styled map with the MapTypeId.
    map.mapTypes.set('dark_map', styledMapTypeDark);
    map.mapTypes.set('light_map', styledMapTypeLight);
    map.setMapTypeId('light_map');

    if (document.querySelector('body').classList.contains("darkly")) {
        map.setMapTypeId('dark_map');
    }

    var bounds = new google.maps.LatLngBounds();
    var circles = [];
    for (var i in data) {
        sensor = data[i];
        if (sensor.averages.max_level === null) {
            continue;
        }
        var position = getPosition(sensor);
        circles.push(addCircle(sensor, position, map));
        bounds.extend(position);
    }

    new AqiClusterer({
        map,
        markers: circles,
        renderer: {render: clusterRender},
        algorithm: new markerClusterer.SuperClusterAlgorithm({minPoints: 4}) });

    google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
        if (this.getZoom() > 15) {
            this.setZoom(15);
        }
    });
    google.maps.event.addListener(map, 'zoom_changed', function(event) {
        var zoom = this.getZoom();
        if (zoom >= 11) {
            factor = 1;
        } else {
            factor = 2 ** (11 - zoom);
        }
        for (var i in circles) {
            circles[i].setRadius(circles[i]._radius * factor);
        }
    });
    map.fitBounds(bounds);
}

return mapsLoaded;
})();
