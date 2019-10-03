<small><?php include('partials/sensors/breadcrumbs.php'); ?></small>
<h4>
    <a href="<?php echo l('main', 'index', $device) ?>"><?php echo $device['description'] ?></a>
</h4>
<?php if (!empty($device['extra_description'])): ?>
    <small><?php echo $device['extra_description'] ?></small>
<?php endif ?>
