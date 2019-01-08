<?php
date_default_timezone_set('Europe/Warsaw');

$sensors = get_sensor_data($device['esp8266id']);
$current_avg_type = '1';
if ($_GET['avg_type'] == '24') {
  $current_avg_type = '24';
}

if ($current_avg_type == '1') {
  $averages = get_avg_sensor_data($device['esp8266id'], 1);
  $pm10_thresholds = PM10_THRESHOLDS_1H;
  $pm25_thresholds = PM25_THRESHOLDS_1H;
  $pm10_limit = PM10_LIMIT_1H;
  $pm25_limit = PM25_LIMIT_1H;
} else {
  $averages = get_avg_sensor_data($device['esp8266id'], 24);
  $pm10_thresholds = PM10_THRESHOLDS_24H;
  $pm25_thresholds = PM25_THRESHOLDS_24H;
  $pm10_limit = PM10_LIMIT_24H;
  $pm25_limit = PM25_LIMIT_24H;
}

if ($averages['PM10'] === null) {
  $pm10_level = null;
  $rel_pm10 = null;
} else {
  $pm10_level = find_level($pm10_thresholds, $averages['PM10']);
  $rel_pm10 = 100 * $averages['PM10'] / $pm10_limit;
}

if ($averages['PM25'] === null) {
  $pm25_level = null;
  $rel_pm25 = null;
} else {
  $pm25_level = find_level($pm25_thresholds, $averages['PM25']);
  $rel_pm25 = 100 * $averages['PM25'] / $pm25_limit;
}

if ($pm10_level === null && $pm25_level === null) {
  $max_level = null;
} else {
  $max_level = max($pm10_level, $pm25_level);
}

?><?php include('partials/head.php'); ?>
<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
    <small>Indeks <a href="https://www.airqualitynow.eu/pl/about_indices_definition.php">CAQI</a>
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
          Brak aktualnych danych
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
          <th scope="col">Nazwa</th>
          <th scope="col" colspan="2">Wartość</th>
          <th scope="col">Indeks</th>
        </tr>
      </thead>
      <tbody>
        <tr class="index-cat-<?php echo $pm25_level ?>">
          <th scope="row">PM<sub>2.5</sub></th>
          <td><?php echo round($averages['PM25'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
          <td><?php echo round($rel_pm25, 0); ?>%</td>
          <td><?php echo POLLUTION_LEVELS[$pm25_level]['name']; ?></td>
        </tr>
        <tr class="index-cat-<?php echo $pm10_level ?>">
          <th scope="row">PM<sub>10</sub></th>
          <td><?php echo round($averages['PM10'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
          <td><?php echo round($rel_pm10, 0); ?>%</td>
          <td><?php echo POLLUTION_LEVELS[$pm10_level]['name']; ?></td>
        </tr>
        <tr>
          <td colspan="4" class="weather-measurements">
            <?php if ($sensors['TEMPERATURE'] !== null): ?>
            <?php echo round($sensors['TEMPERATURE'], 1) ?> &deg;C
            <?php endif ?>
            <?php if ($sensors['PRESSURE'] !== null): ?>
            | <?php echo round($sensors['PRESSURE'], 0) ?> hPa
            <?php endif ?>
            <?php if ($sensors['HUMIDITY'] !== null): ?>
            | <?php echo round($sensors['HUMIDITY'], 0) ?>% wilgotności
            <?php endif ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php endif ?>
    <div class="graph-container" data-range="day" data-type="pm" data-avg-type="<?php echo $current_avg_type ?>">
      <canvas class="graph"></canvas>
      Ostatnia aktualizacja: <?php echo date("Y-m-d H:i:s", $sensors['last_update']); ?>.</small>
    </div>
  </div>
</div>
<?php include('partials/tail.php'); ?>