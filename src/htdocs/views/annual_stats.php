<p></p>
<div class="row">
</div>

<div class="row">
  <div class="col-md-8 offset-md-2">
    <h4><?php echo __('Annual stats') ?> - <?php echo $device['description'] ?></h4>
  </div>
</div>

<div class="row">
  <div class="col-md-8 offset-md-2">
    <h5><?php echo __('Annual averages') ?></h5>
    <table class="table">
      <thead>
        <tr>
          <th scope="col"><?php echo __('Name') ?></th>
          <th scope="col"><?php echo __('Annual average') ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">PM<sub>2.5</sub></th>
          <td><?php echo round($averages['pm25'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
        </tr>
        <tr>
          <th scope="row">PM<sub>10</sub></th>
          <td><?php echo round($averages['pm10'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="row annual-graphs" data-json-uri="<?php echo l('annual_stats', 'get_data') ?>">
  <div class="col-md-8 offset-md-2">
    <h5><?php echo __('Pollution levels by days') ?></h5>
    <div class="annual-graph-container">
      <canvas class="graph"></canvas>
    </div>
  </div>
</div>
