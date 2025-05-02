var mapsLoaded = (function() {
	const COLORS = [
		"#57b108",
		"#b0dd10",
		"#ffd911",
		"#e58100",
		"#990000"
	];

	const average = array => array.reduce((a, b) => a + b) / array.length;

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

	function createInfoWindow(marker, path) {
		var request = new XMLHttpRequest();
		request.open('GET', path, true);
		request.onload = function() {
			if (request.status == 200) {
				const popup = L.popup().setContent(request.responseText);
				marker.bindPopup(popup).openPopup();
			}
		};
		request.send();
	}
	
	function createMarker(sensor) {
		const color = COLORS[sensor.averages.max_level];
		const marker = L.circle(
			[sensor.lat, sensor.lng], {
				color: color,
				payload: {
					level: sensor.averages.max_level,
					radius: sensor.radius,
				}
			}
		)
		marker.on('click', function(e) {
			createInfoWindow(this, sensor.info_path);
		});
		return marker;
	}

	function createClusterIcon(cluster) {
		const levels = cluster.getAllChildMarkers().map((m) => m.options.payload.level);
		const avg_level = Math.floor(average(levels));
		const color = COLORS[avg_level];
		const svg = window.btoa(`
<svg fill="${color}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240">
<circle cx="120" cy="120" opacity=".6" r="70" />
<circle cx="120" cy="120" opacity=".3" r="90" />
<circle cx="120" cy="120" opacity=".2" r="110" />
</svg>`);
		return L.divIcon({
			html: `
				<div class="cluster-container">
					<img src="data:image/svg+xml;base64,${svg}"/>
					<div class="cluster-label">${cluster.getChildCount()}</div>
				</div>
			`,
			iconSize: L.point(45, 45),
			className: 'cluster-icon',
		});
	}
	
	function initMap(mapDiv, data) {
		L.Icon.Default.imagePath = '/public/img/';

		const map = L.map(mapDiv);//.setView([data[0].lat, data[1].lng], 3);
		L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);

		const clusterGroup = L.markerClusterGroup({
			iconCreateFunction: createClusterIcon,
			spiderfyOnEveryZoom: false,
			spiderfyOnMaxZoom: false,
			showCoverageOnHover: false,
		});
		const positions = [];
		const circles = [];
		for (var i in data) {
			sensor = data[i];
			if (sensor.averages.max_level === null) {
				continue;
			}
			const marker = createMarker(sensor);
			circles.push(marker);
			clusterGroup.addLayer(marker);
			positions.push([sensor.lat, sensor.lng]);
		}
		map.on('zoomend', function() {
			const zoom = map.getZoom();
			if (zoom >= 11) {
				factor = 1;
			} else {
				factor = 2 ** (11 - zoom);
			}
			for (var i in circles) {
				const marker = circles[i];
				marker.setRadius(marker.options.payload.radius * factor);
			}
		});

		var bounds = new L.LatLngBounds(positions);
		map.fitBounds(bounds);	
		map.addLayer(clusterGroup);
	}

	return mapsLoaded;
})();

mapsLoaded();