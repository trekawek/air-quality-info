<p></p>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <h4><?php echo $currentDevice['description'] ?></h4>
        <p></p>
        <h5><?php echo __('Debug info') ?></h5>

        <p><a class="btn btn-primary" href="<?php echo l('debug', 'index'); ?>"><?php echo __('Last data') ?></a></p>

        <p><a class="btn btn-primary" href="<?php echo l('debug', 'index_json'); ?>"><?php echo __('Received JSONs') ?></a></p>

        <h5><?php echo __('Maintenance tools') ?></h5>
        <?php if ($db_type === 'mysql'): ?>
        <p>
        <form method="POST" action="<?php echo l('tool', 'migrate_madavi') ?>"><button type="submit" class="btn btn-danger"><?php echo __('Import from Madavi.de') ?></button></form>
        <small><?php echo __('Imports data from') ?> <a href="https://www.madavi.de/sensor/csvfiles.php">https://www.madavi.de/sensor/csvfiles.php</a></small>
        </p>

        <p>
        <form method="POST" action="<?php echo l('tool', 'migrate_rrd_to_mysql') ?>"><button type="submit" class="btn btn-danger"><?php echo __('Migrate old RRD files to MySQL') ?></button></form>
        <small><?php echo __('Migrates data from old RRD to the currently configured MySQL') ?></small>
        </p>
        <?php endif ?>

        <?php if ($db_type === 'rrd'): ?>
        <p>
        <form method="POST" action="<?php echo l('tool', 'update_rrd_schema') ?>"><button type="submit" class="btn btn-danger"><?php echo __('Update RRD schema') ?></button></form>
        <small><?php echo __('Updates the RRD schema, if they were any changes') ?></small>
        </p>
        <?php endif ?>
    </div>
</div>
