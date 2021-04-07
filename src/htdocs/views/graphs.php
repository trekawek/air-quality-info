<p></p>

<div class="row device-header">
    <div class="col-md-3 offset-md-2">
        <?php include('partials/device_description.php'); ?>
    </div>
    <div class="col-md-2 text-center">
      <h4><?php echo __('Graphs') ?></h4>
    </div>
</div>

<p></p>

<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="btn-group btn-group-sm graph-range" role="group" aria-label="<?php echo __('Range') ?>">
      <button type="button" class="btn btn-primary" data-range="day"><?php echo __('Day') ?></button>
      <button type="button" class="btn btn-secondary" data-range="week"><?php echo __('Week') ?></button>
      <button type="button" class="btn btn-secondary" data-range="month"><?php echo __('Month') ?></button>
      <button type="button" class="btn btn-secondary" data-range="year"><?php echo __('Year') ?></button>
    </div>
    <div class="btn-group btn-group-sm graph-avg-type" role="group" aria-label="<?php echo __('Moving average') ?>">
      <button type="button" class="btn btn-secondary" data-avg-type="0"><?php echo __('Instantaneous') ?></button>
      <button type="button" class="btn btn-secondary" data-avg-type="0.25">15m</button>
      <button type="button" class="btn btn-primary" data-avg-type="1">1h</button>
      <button type="button" class="btn btn-secondary" data-avg-type="24">24h</button>
      <button type="button" class="btn btn-secondary" data-avg-type="720">30d</button>
    </div>
  </div>
</div>

<p></p>

<?php if (count($graphs) === 1): ?>
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="graph-container" data-range="day" data-type="<?php echo $graphs[0] ?>" data-avg-type="1" data-graph-uri="<?php echo l('graph', 'get_data')?>" >
      <canvas class="graph"></canvas>
    </div>
  </div>
</div>
<?php else: ?>
<?php
foreach($graphs as $i => $type):
?>
<?php if ($i % 2 == 0): ?>
<div class="row">
<?php endif ?>
  <div class="col-md-6 text-center">
    <div class="graph-container" data-range="day" data-type="<?php echo $type ?>" data-avg-type="1" data-graph-uri="<?php echo l('graph', 'get_data')?>" >
      <canvas class="graph"></canvas>
    </div>
  </div>
<?php if ($i % 2 == 1): ?>
</div>
<?php endif ?>
<?php endforeach ?>

<?php if (count($graphs) % 2 == 1): // missing div if there's an odd count of graphs ?>
</div>
<?php endif ?>

<?php endif ?>
