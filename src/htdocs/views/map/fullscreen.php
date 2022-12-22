<div class="w-100 h-100 position-absolute">
    <div id="map" class="h-100" data-hide-controls="true" data-url="<?php echo l('map', 'data') ?>"></div>
</div>

<?php echo jsLink("public/js/map.js", "defer"); ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo CONFIG['google_maps_key'] ?>&callback=mapsLoaded" async defer></script>
