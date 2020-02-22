<?php if ($homeWidget['level'] !== null && isset($this->user['sensor_widget']) && $this->user['sensor_widget']): ?>
<?php include('views/widget/homewidget.php') ?>
<?php endif ?>

<?php include('partials/sensors/avg-switch.php') ?>

<div class="row device-header">
    <div class="col-md-3 offset-md-2">
        <?php include('partials/device_description.php'); ?>
    </div>
    <div class="col-md-2 text-center">
        <?php include('partials/sensors/badge.php') ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
        <?php include('partials/sensors/table.php') ?>
    </div>
</div>

<div class="row">
  <div class="col-md-8 offset-md-2 text-center">
  <h4><?php echo __('Daily graph') ?></h4>
  <?php if ($sensors['pm10'] !== null || $sensors['pm25'] !== null): ?>
    <div class="graph-container" data-range="<?php echo $currentAvgType == 24 ? 'week' : 'day' ?>" data-type="pm" data-avg-type="<?php echo $currentAvgType ?>" data-graph-uri="<?php echo l('graph', 'get_data')?>" >
      <canvas class="graph"></canvas>
    </div>
  <?php endif ?>
  </div>
</div>