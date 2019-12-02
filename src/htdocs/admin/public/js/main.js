'use strict';

const CONFIG = document.querySelector('body').dataset;
const LOCALE = JSON.parse(CONFIG.locale);

function __(msg) {
    if (typeof LOCALE[msg] != 'undefined') {
        return LOCALE[msg];
    } else {
        if (CONFIG.currentLang != 'en') {
            console.log("Unknown msg: [" + msg + "] for locale [" + CONFIG.currentLang +"]");
        }
        return msg;
    }
}

document.querySelectorAll('.delete-link').forEach(link => {
	link.onclick = (e => {
        if (!confirm(__('Are you sure to delete this resource?'))) {
            return false;
        }
		var request = new XMLHttpRequest();
    	request.open('DELETE', link.href, true);
        request.onload = function () {
            location.href = location.href;
        }
		request.send(null);
		return false;
	})
});

document.querySelectorAll('.post-with-output').forEach(link => {
	link.onclick = (e => {
        var logs = document.querySelector(link.dataset.output);
        var finishedBadge = document.querySelector(link.dataset.onSuccess);

        var data = 'csrf_token=' + link.dataset.csrfToken;

        var request = new XMLHttpRequest();
        request.seenBytes = 0;
        request.open('POST', link.href, true);
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.onreadystatechange = function() { 
            if (request.readyState > 2) {
                var newData = request.responseText.substr(request.seenBytes); 
                logs.innerHTML += newData;
                logs.scrollTop = logs.scrollHeight;
                request.seenBytes = request.responseText.length;
            }
            if (request.readyState == 4) {
                finishedBadge.classList.remove('d-none');
            }
        };
        logs.innerHTML += "Sending request...\n";
		request.send(data);
		return false;
	})
});

(function() {
    function updateAltitude(location, elevInput) {
        var elevator = new google.maps.ElevationService;
        elevator.getElevationForLocations({
            'locations': [location]
        }, function(results, status) {
            if (results.length > 0 && results[0].elevation !== null) {
                elevationInput.value = Math.round(results[0].elevation);
            }
        });
    }
    
    document.querySelectorAll('.map-group').forEach(element => {
        var latInput = document.querySelector(element.dataset.inputLat);
        var lngInput = document.querySelector(element.dataset.inputLng);
        var elevInput = document.querySelector(element.dataset.inputElevation);
        var pickerConfig = {
            setCurrentPosition: true
        };
        var mapsConfig = {
            zoom: 15,
            streetViewControl: false
        }
        if (!!latInput.value) {
            pickerConfig.lat = latInput.value;
        }
        if (!!lngInput.value) {
            pickerConfig.lng = lngInput.value;
        }
        var lp = new locationPicker(document.querySelector('.map'), pickerConfig, mapsConfig);
        google.maps.event.addListener(lp.map, 'idle', function (event) {
            var location = lp.getMarkerPosition();
            latInput.value = location.lat;
            lngInput.value = location.lng;
        });
        google.maps.event.addListener(lp.map, 'dragend', function (event) {
            var location = lp.getMarkerPosition();
            updateAltitude(location, elevInput);
        });
    });
})();