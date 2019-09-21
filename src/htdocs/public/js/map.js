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

function createInfoWindow(sensor, map, marker) {
    var request = new XMLHttpRequest();
    request.open('GET', sensor.info_path, true);
    request.onload = function() {
        if (request.status == 200) {
            var infoWindow = new google.maps.InfoWindow({
                content: request.responseText
            })
            infoWindow.open(map, marker);
        }
    };
    request.send();
}

function addMarker(sensor, position, map) {
    var marker = new google.maps.Marker({
        position: position,
        map: map
    });
    marker.addListener('click', function() {
        createInfoWindow(sensor, map, marker);
    });
}

function addCircle(sensor, position, map) {
    if (sensor.averages.max_level === null) {
        return;
    }
    new google.maps.Circle({
        strokeColor: COLORS[sensor.averages.max_level],
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: COLORS[sensor.averages.max_level],
        fillOpacity: 0.35,
        map: map,
        center: position,
        radius: sensor.radius
    });
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
    for (var i in data) {
        sensor = data[i];
        var position = getPosition(sensor);
        addMarker(sensor, position, map);
        addCircle(sensor, position, map);
        bounds.extend(position);
    }
    google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
        this.setZoom(map.getZoom()-1);
        if (this.getZoom() > 15) {
            this.setZoom(15);
        }
    });      
    map.fitBounds(bounds);
}

return mapsLoaded;
})();