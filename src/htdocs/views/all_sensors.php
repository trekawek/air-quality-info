<?php include('partials/sensors/avg-switch.php');
foreach($data as $r):
extract($r);
?>
<div class="row">
    <div class="col-md-3 offset-md-2">
        <h4>
            <a href="<?php echo l('main', 'index', $device) ?>"><?php echo $device['description'] ?></a>
        </h4>
        <?php include('partials/sensors/breadcrumbs.php'); ?>
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