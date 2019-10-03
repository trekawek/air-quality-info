<?php include('partials/sensors/avg-switch.php');
foreach($data as $r):
extract($r);
?>
<div class="row device-header">
    <div class="col-md-3 offset-md-2">
        <small><?php include('partials/sensors/breadcrumbs.php'); ?></small>
        <h4>
            <a href="<?php echo l('main', 'index', $device) ?>"><?php echo $device['description'] ?></a>
        </h4>
        <?php if (!empty($device['extra_description'])): ?>
        <small><?php echo $device['extra_description'] ?></small>
        <?php endif ?>
    </div>
    <div class="col-md-2 text-center">
        <?php include('partials/sensors/badge.php') ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
        <?php
        include('partials/sensors/table.php');
        ?>
    </div>
</div>
<?php endforeach ?>