<div class="text-center">
    <?php if(isset($breadcrumbs) && count($breadcrumbs) > 2): ?>
    <small><?php include('partials/sensors/breadcrumbs.php'); ?></small><br/>
    <?php endif ?>

    <a href="<?php echo $deviceUrl ?>"><?php echo $device['description'] ?></a>

    <?php if (!empty($device['extra_description'])): ?>
    <br/><small><?php echo $device['extra_description'] ?></small>
    <?php endif ?>

    <?php include('partials/sensors/badge.php') ?>
</div>

<?php
include('partials/sensors/table.php');
?>