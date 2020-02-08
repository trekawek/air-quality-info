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
        map: map,
        center: position,
        radius: sensor.radius
    });
    circle.addListener('click', function() {
        createInfoWindow(sensor, map, position);
    });
    circle._radius = sensor.radius;
    return circle;
}

function initMap(mapDiv, data) {
    if (data.length == 0) {
        return;
    }

    var styledMapType = new google.maps.StyledMapType(
      [
          {
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#242f3e"
              }
            ]
          },
          {
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#746855"
              }
            ]
          },
          {
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

    var initPosition = getPosition(data[0]);
    var map = new google.maps.Map(mapDiv, {
        zoom: 15,
        mapTypeControlOptions: {
            mapTypeIds: ['roadmap', 'satellite', 'styled_map']
        },
        center: initPosition,
        streetViewControl: false
    });

    //Associate the styled map with the MapTypeId.
    map.mapTypes.set('styled_map', styledMapType);

    //Set styled map to display
    //map.setMapTypeId('styled_map');

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
