<?php include('partials/sensors/avg-switch.php');
foreach($data as $r):
extract($r);
?>
<div class="row">
    <div class="col-md-3 offset-md-2">
        <small><?php echo implode(' / ', array_slice($desc, 0, -1)) ?></small>
        <h4>
            <a href="<?php echo l('main', 'index', $device) ?>"><?php echo end($desc) ?></a>
        </h4>
    </div>
    <div class="col-md-2 text-center">
        <?php include('partials/sensors/badge.php') ?>
    </div>
</div>

<?php
include('partials/sensors/table.php');
endforeach
?>