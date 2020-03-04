<p></p>
<div class="row">
    <div class="col-md-12">
        <div id="map" data-url="<?php echo l('map', 'data') ?>"></div>
    </div>
</div>
<?php echo jsLink("public/js/map.js"); ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo CONFIG['google_maps_key'] ?>&callback=mapsLoaded" async defer></script>
