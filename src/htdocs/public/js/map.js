var mapsLoaded = (function() {

const COLORS = [
    "#57b108",
    "#b0dd10",
    "#ffd911",
    "#e58100",
    "#990000"
];

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

    var initPosition = getPosition(data[0]);
    var map = new google.maps.Map(mapDiv, {
        zoom: 15,
        center: initPosition,
        streetViewControl: false
    });
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
    google.maps.event.addListener(map, 'zoom_changed', function(event) {
        var zoom = this.getZoom();
        var factors = {
            0: 2048,
            1: 1024,
            2: 512,
            3: 256,
            4: 128,
            5: 64,
            6: 32,
            7: 16,
            8: 8,
            9: 4,
            10: 2
        }
        var factor;
        if (zoom >= 11) {
            factor = 1;
        } else {
            factor = factors[zoom];
        }
        console.log(zoom, factor);
        for (var i in circles) {
            circles[i].setRadius(circles[i]._radius * factor);
        }
    });
    map.fitBounds(bounds);
}

return mapsLoaded;
})();