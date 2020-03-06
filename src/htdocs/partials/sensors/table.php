<?php if ($averages['pm25_level'] !== null || $averages['pm10_level'] !== null): ?>
<table class="table">
  <thead>
    <tr>
      <th scope="col"><?php echo __('Name') ?></th>
      <th scope="col" colspan="2"><?php echo __('Value') ?></th>
      <th scope="col"><?php echo __('Index') ?></th>
    </tr>
  </thead>
  <tbody>
    <tr class="index-cat-<?php echo $averages['pm25_level'] ?>">
      <th scope="row">PM<sub>2.5</sub></th>
      <td><?php echo round($averages['values']['pm25'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
      <td><?php echo round($averages['rel_pm25'], 0); ?>%</td>
      <td><?php echo __(\AirQualityInfo\Lib\PollutionLevel::POLLUTION_LEVELS[$averages['pm25_level']]['name']); ?></td>
    </tr>
    <tr class="index-cat-<?php echo $averages['pm10_level'] ?>">
      <th scope="row">PM<sub>10</sub></th>
      <td><?php echo round($averages['values']['pm10'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
      <td><?php echo round($averages['rel_pm10'], 0); ?>%</td>
      <td><?php echo __(\AirQualityInfo\Lib\PollutionLevel::POLLUTION_LEVELS[$averages['pm10_level']]['name']); ?></td>
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
        if (isset($averages['values']['co2'])) {
          $weather[] = 'CO<sub>2</sub> '.round($averages['values']['co2'], 0).' ppm';
        }
        if (!empty($weather)): ?>
        <?php echo implode(' | ', $weather) ?>
        <br/>
        <?php endif ?>
        <small><?php echo __('Last update') ?>: <?php echo date("Y-m-d H:i:s", $sensors['last_update']); ?>.</small>
      </td>
    </tr>
  </tbody>
</table>
<?php endif ?>