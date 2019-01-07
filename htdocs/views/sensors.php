<?php
date_default_timezone_set('Europe/Warsaw');

$sensors = get_sensor_data($device['esp8266id']);
$max_level = null;
if (isset($device['value_mapping']['pm10'])) {
  $pm10_level = find_level(PM10_THRESHOLDS, $sensors['PM10']);
  $max_level = $pm10_level;
} else {
  $pm10_level = null;
}
if (isset($device['value_mapping']['pm25'])) {
  $pm25_level = find_level(PM25_THRESHOLDS, $sensors['PM25']);
  $max_level = $pm25_level;
} else {
  $pm25_level = null;
}
if ($pm10_level !== null && $pm25_level !== null) {
  $max_level = max($pm10_level, $pm25_level);
}
?><?php include('partials/head.php'); ?>
<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
    <small>Poziom zanieczyszczenia <a href="https://www.airqualitynow.eu/pl/about_indices_definition.php">CAQI</a>:</small>
    <h2>
      <span class="badge index-cat-<?php echo $max_level; ?>">
        <?php echo POLLUTION_LEVELS[$max_level]['name']; ?>
      </span>
    </h2>
  </div>
</div>

<div class="row">
  <div class="col-md-8 offset-md-2 text-center">
    <table class="table">
      <?php if ($pm10_level !== null || $pm25_level !== null): ?>
      <thead>
        <tr>
          <th scope="col">Nazwa</th>
          <th scope="col">Wartość</th>
          <th scope="col">Indeks</th>
          <th scope="col">Procent <a href="http://ec.europa.eu/environment/air/quality/standards.htm">normy</a></th>
        </tr>
      </thead>
      <?php endif ?>
      <tbody>
        <?php if ($pm25_level !== null): ?>
        <tr class="index-cat-<?php echo $pm25_level; ?>">
          <th scope="row">PM<sub>2.5</sub></th>
          <td><?php echo round($sensors['PM25'], 0); ?> <small>µg/m<sup>3</sup></small></td>
          <td><?php echo POLLUTION_LEVELS[$pm25_level]['name'] ?></td>
          <td><?php echo round(100 * $sensors['PM25'] / PM25_LIMIT, 0); ?>%</td>
        </tr>
        <?php endif ?>
        <?php if ($pm10_level !== null): ?>
        <tr class="index-cat-<?php echo $pm10_level; ?>">
          <th scope="row">PM<sub>10</sub></th>
          <td><?php echo round($sensors['PM10'], 0); ?> <small>µg/m<sup>3</sup></small></td>
          <td><?php echo POLLUTION_LEVELS[$pm10_level]['name'] ?></td>
          <td><?php echo round(100 * $sensors['PM10'] / PM10_LIMIT, 0); ?>%</td>
        </tr>
        <?php endif ?>
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