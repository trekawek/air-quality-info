<p></p>
<div class="row">
</div>

<div class="row">
  <div class="col-md-8 offset-md-2">
    <h4><?php echo __('Annual stats') ?></h4>
  </div>
</div>

<div class="row annual-graphs" data-json-uri="<?php echo l('annual_stats', 'get_data') ?>">
  <div class="col-md-6 text-center">
    <div class="annual-graph-container" data-type="pm10">
      <canvas class="graph"></canvas>
    </div>
  </div>
  <div class="col-md-6 text-center">
    <div class="annual-graph-container" data-type="pm25">
      <canvas class="graph"></canvas>
    </div>
  </div>
</div>
