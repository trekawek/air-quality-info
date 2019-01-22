<?php
$sensors = $dao->getLastData();

$current_avg_type = '1';
if (isset($_GET['avg_type']) && $_GET['avg_type'] == '24') {
  $current_avg_type = '24';
}

if ($current_avg_type == '1') {
  $averages = $dao->getLastAvg(1);
  $pm10_thresholds = PM10_THRESHOLDS_1H;
  $pm25_thresholds = PM25_THRESHOLDS_1H;
  $pm10_limit = PM10_LIMIT_1H;
  $pm25_limit = PM25_LIMIT_1H;
} else {
  $averages = $dao->getLastAvg(24);
  $pm10_thresholds = PM10_THRESHOLDS_24H;
  $pm25_thresholds = PM25_THRESHOLDS_24H;
  $pm10_limit = PM10_LIMIT_24H;
  $pm25_limit = PM25_LIMIT_24H;
}

if ($averages['pm10'] === null) {
  $pm10_level = null;
  $rel_pm10 = null;
} else {
  $pm10_level = find_level($pm10_thresholds, $averages['pm10']);
  $rel_pm10 = 100 * $averages['pm10'] / $pm10_limit;
}

if ($averages['pm25'] === null) {
  $pm25_level = null;
  $rel_pm25 = null;
} else {
  $pm25_level = find_level($pm25_thresholds, $averages['pm25']);
  $rel_pm25 = 100 * $averages['pm25'] / $pm25_limit;
}

if ($pm10_level === null && $pm25_level === null) {
  $max_level = null;
} else {
  $max_level = max($pm10_level, $pm25_level);
}

?><?php include('partials/head.php'); ?>
<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
    <small><?php echo __('<a href="https://www.airqualitynow.eu/about_indices_definition.php">CAQI</a> index') ?>
    (<?php
        foreach (array('1' => '1h', '24' => '24h') as $value => $name) {
          if ($current_avg_type == $value) {
            echo "<strong>$name</strong>";
          } else {
            echo "<a href=\"".l($device, 'sensors', array('avg_type' => $value))."\">$name</a>";
          }
          if ($value != '24') {
            echo " / ";
          }
      }?>):
    </small>
    <h2>
      <?php if ($max_level !== null): ?>
      <span class="badge index-cat-<?php echo $max_level; ?>">
        <?php echo POLLUTION_LEVELS[$max_level]['name']; ?>
      </span>
      <?php else: ?>
        <span class="badge badge-dark">
          <?php echo __('There are no data') ?>
        </span>
      <?php endif ?>
    </h2>
    <small>
    </small>
  </div>
</div>

<div class="row">
  <div class="col-md-8 offset-md-2 text-center">
    <?php if ($pm25_level !== null || $pm10_level !== null): ?>
    <table class="table">
      <thead>
        <tr>
          <th scope="col"><?php echo __('Name') ?></th>
          <th scope="col" colspan="2"><?php echo __('Value') ?></th>
          <th scope="col"><?php echo __('Index') ?></th>
        </tr>
      </thead>
      <tbody>
        <tr class="index-cat-<?php echo $pm25_level ?>">
          <th scope="row">PM<sub>2.5</sub></th>
          <td><?php echo round($averages['pm25'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
          <td><?php echo round($rel_pm25, 0); ?>%</td>
          <td><?php echo POLLUTION_LEVELS[$pm25_level]['name']; ?></td>
        </tr>
        <tr class="index-cat-<?php echo $pm10_level ?>">
          <th scope="row">PM<sub>10</sub></th>
          <td><?php echo round($averages['pm10'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
          <td><?php echo round($rel_pm10, 0); ?>%</td>
          <td><?php echo POLLUTION_LEVELS[$pm10_level]['name']; ?></td>
        </tr>
        <tr>
          <td colspan="4" class="weather-measurements">
            <?php
            $weather = array();
            if ($sensors['temperature'] !== null) {
              $weather[] = '<i class="wi wi-thermometer"></i> '.round($sensors['temperature'], 1).' &deg;C';
            }
            if ($sensors['pressure'] !== null) {
              $weather[] = '<i class="wi wi-barometer"></i> '.round($sensors['pressure'], 0).' hPa';
            }
            if ($sensors['humidity'] !== null) {
              $weather[] = '<i class="wi wi-humidity"></i> '.round($sensors['humidity'], 0).'%';
            }
            echo implode(' | ', $weather)
            ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php endif ?>
    <?php if ($sensors['pm10'] !== null || $sensors['pm25'] !== null): ?>
    <div class="graph-container" data-range="day" data-type="pm" data-avg-type="<?php echo $current_avg_type ?>">
      <canvas class="graph"></canvas>
      <?php echo __('Last update') ?>: <?php echo date("Y-m-d H:i:s", $sensors['last_update']); ?>.</small>
    </div>
    <?php endif ?>
  </div>
</div>
<?php include('partials/tail.php'); ?>
