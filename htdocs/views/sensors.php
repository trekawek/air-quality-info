<?php
date_default_timezone_set('Europe/Warsaw');

$sensors = get_sensor_data($device['esp8266id']);
$avg_24h = get_avg_sensor_data($device['esp8266id'], 24);
$avg_1h = get_avg_sensor_data($device['esp8266id'], 24);

$pm10_1h = find_level(PM10_THRESHOLDS_1H, $avg_1h['PM10']);
$pm25_1h = find_level(PM25_THRESHOLDS_1H, $avg_1h['PM25']);
$max_1h = max($pm10_1h, $pm25_1h);

$pm10_24h = find_level(PM10_THRESHOLDS_24H, $avg_1h['PM10']);
$pm25_24h = find_level(PM25_THRESHOLDS_24H, $avg_1h['PM25']);
?><?php include('partials/head.php'); ?>
<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
    <small>Indeks <a href="https://www.airqualitynow.eu/pl/about_indices_definition.php">CAQI</a> (ostatnia godziny):</small>
    <h2>
      <span class="badge index-cat-<?php echo $max_1h; ?>">
        <?php echo POLLUTION_LEVELS[$max_1h]['name']; ?>
      </span>
    </h2>
  </div>
</div>

<div class="row">
  <div class="col-md-8 offset-md-2 text-center">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Nazwa</th>
          <th scope="col" colspan="3">Wartość</th>
        </tr>
      </thead>
      <thead>
        <tr>
          <th scope="col">&nbsp;</th>
          <th scope="col">Chwilowa</th>
          <th scope="col">1h</th>
          <th scope="col">24h</th>
        </tr>
      </thead>
      <tbody>
        <tr class="index-cat-<?php echo max($pm25_1h, $pm25_24h); ?>">
          <th scope="row">PM<sub>2.5</sub></th>
          <td><?php echo round($sensors['PM25'], 0); ?> <small>µg/m<sup>3</sup></small></td>
          <td class="index-cat-<?php echo $pm25_1h; ?>"><?php echo round($avg_1h['PM25'], 0); ?> <small>µg/m<sup>3</sup></small></td>
          <td class="index-cat-<?php echo $pm25_24h; ?>"><?php echo round($avg_24h['PM25'], 0); ?> <small>µg/m<sup>3</sup></small></td>
        </tr>
        <tr class="index-cat-<?php echo max($pm10_1h, $pm10_24h); ?>">
          <th scope="row">PM<sub>10</sub></th>
          <td><?php echo round($sensors['PM10'], 0); ?> <small>µg/m<sup>3</sup></small></td>
          <td class="index-cat-<?php echo $pm10_1h; ?>"><?php echo round($avg_1h['PM10'], 0); ?> <small>µg/m<sup>3</sup></small></td>
          <td class="index-cat-<?php echo $pm10_24h; ?>"><?php echo round($avg_24h['PM10'], 0); ?> <small>µg/m<sup>3</sup></small></td>
        </tr>
        <tr>
          <td colspan="4">
            <?php if ($sensors['TEMPERATURE'] !== null): ?>
            <small>Temperatura: </small><?php echo round($sensors['TEMPERATURE'], 1) ?> &deg;C
            <?php endif ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <?php if ($sensors['PRESSURE'] !== null): ?>
            <small>Ciśnienie: </small><?php echo round($sensors['PRESSURE'], 0) ?> hPa
            <?php endif ?>
          </td>
          <td colspan="2">
            <?php if ($sensors['HUMIDITY'] !== null): ?>
            <small>Wilgotność: </small><?php echo round($sensors['HUMIDITY'], 0) ?>%
            <?php endif ?>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="graph-container" data-range="day" data-type="pm">
      <canvas class="graph"></canvas>
      <small><a href="<?php echo l($device, 'graphs'); ?>">Zobacz wszystkie wykresy</a>.<br/>Ostatnia aktualizacja: <?php echo date("Y-m-d H:i:s", $sensors['last_update']); ?>.</small>
    </div>
  </div>
</div>
<?php include('partials/tail.php'); ?>