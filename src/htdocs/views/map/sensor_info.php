<div class="text-center">
    <a href="<?php echo l('main', 'index', $device) ?>"><?php echo $device['description'] ?></a>
    <?php include('partials/sensors/badge.php') ?>
</div>

<?php
include('partials/sensors/table.php');
?>