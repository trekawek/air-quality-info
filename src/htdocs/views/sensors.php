<?php
$sensors = $dao->getLastData();

$current_avg_type = '1';
if (isset($_GET['avg_type']) && $_GET['avg_type'] == '24') {
  $current_avg_type = '24';
}

$averages = getAverages($dao, $sensors, $current_avg_type);
?>
<?php include('partials/sensors/avg-switch.php') ?>

<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
        <?php include('partials/sensors/badge.php') ?>
    </div>
</div>

<?php include('partials/sensors/table.php') ?>

<div class="row">
  <div class="col-md-8 offset-md-2 text-center">
  <h4><?php echo __('Daily graph') ?></h4>
  <?php if ($sensors['pm10'] !== null || $sensors['pm25'] !== null): ?>
    <div class="graph-container" data-range="<?php echo $current_avg_type == 24 ? 'week' : 'day' ?>" data-type="pm" data-avg-type="<?php echo $current_avg_type ?>" data-graph-uri="<?php echo l($device, 'graph_data.json')?>" >
      <canvas class="graph"></canvas>
    </div>
  <?php endif ?>
  </div>
</div>