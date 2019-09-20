<p></p>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div id="map" data-url="<?php echo l('map', 'data') ?>"></div>
    </div>
</div>
<script src="/public/js/map.js?v=31"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo CONFIG['google_maps_key'] ?>&callback=mapsLoaded" async defer></script>