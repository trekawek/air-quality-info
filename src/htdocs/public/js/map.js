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
    google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
        this.setZoom(map.getZoom()-1);
        if (this.getZoom() > 15) {
            this.setZoom(15);
        }
    });
    google.maps.event.addListener(map, 'zoom_changed', function(event) {
        var zoom = this.getZoom();
        var radius = {
            0: 10_000,
            1: 10_000,
            2: 10_000,
            3: 10_000,
            4: 10_000,
            5: 10_000,
            6: 10_000,
            7: 5_000,
            8: 2_000,
            9: 1_000,
            10: 750
        }
        console.log(zoom);
        if (zoom >= 11) {
            for (var i in circles) {
                circles[i].setRadius(circles[i]._radius);
            }
        } else {
            for (var i in circles) {
                circles[i].setRadius(radius[zoom]);
            }
        }
    });
    map.fitBounds(bounds);
}

return mapsLoaded;
})();