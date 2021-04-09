<?php if ($averages['pm25_level'] !== null || $averages['pm10_level'] !== null): ?>
<table class="table">
  <thead>
    <tr>
      <th scope="col"><?php echo __('Name') ?></th>
      <th scope="col" colspan="2"><?php echo __('Value') ?></th>
      <th scope="col">
        <?php
          $displayNumberConcentrationLabel = false;
          foreach (array('n05', 'n1', 'n25', 'n4', 'n10') as $n) {
            if ($averages['values'][$n] > 0) {
              $displayNumberConcentrationLabel = true;
            }
          }
        ?>
        <?php if ($displayNumberConcentrationLabel): ?>
          <?php echo __('Number concentration') ?>
        <?php endif ?>
      </th>
      <th scope="col"><?php echo __('Index') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if ($averages['values']['pm25'] > 0): ?>
    <tr class="index-cat-<?php echo $averages['pm25_level'] ?>">
      <th scope="row">PM<sub>2.5</sub></th>
      <td><?php echo round($averages['values']['pm25'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
      <td><?php echo round($averages['rel_pm25'], 0); ?>%</td>
      <td>
        <?php if ($averages['values']['n25'] > 0): ?>
        <?php echo round($averages['values']['n25'], 0); ?><small>/cm<sup>3</sup></small>
        <?php endif ?>
      </td>
      <td><?php echo __(\AirQualityInfo\Lib\PollutionLevel::POLLUTION_LEVELS[$averages['pm25_level']]['name']); ?></td>
    </tr>
    <?php endif ?>
    <?php if ($averages['values']['pm10'] > 0): ?>
    <tr class="index-cat-<?php echo $averages['pm10_level'] ?>">
      <th scope="row">PM<sub>10</sub></th>
      <td><?php echo round($averages['values']['pm10'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small></td>
      <td><?php echo round($averages['rel_pm10'], 0); ?>%</td>
      <td>
        <?php if ($averages['values']['n10'] > 0): ?>
        <?php echo round($averages['values']['n10'], 0); ?><small>/cm<sup>3</sup></small>
        <?php endif ?>
      </td>
      <td><?php echo __(\AirQualityInfo\Lib\PollutionLevel::POLLUTION_LEVELS[$averages['pm10_level']]['name']); ?></td>
    </tr>
    <?php endif ?>
    <?php if ($averages['values']['n05'] > 0): ?>
    <tr>
      <th scope="row">PM<sub>0.5</sub></th>
      <td></td>
      <td></td>
      <td>
        <?php echo round($averages['values']['n05'], 0); ?><small>/cm<sup>3</sup></small>
      </td>
      <td></td>
    </tr>
    <?php endif ?>
    <?php if ($averages['values']['pm1'] > 0 || $averages['values']['n1'] > 0): ?>
    <tr>
      <th scope="row">PM<sub>1</sub></th>
      <td>
        <?php if ($averages['values']['pm1'] > 0): ?>
        <?php echo round($averages['values']['pm1'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small>
        <?php endif ?>
      </td>
      <td></td>
      <td>
        <?php if ($averages['values']['n1'] > 0): ?>
        <?php echo round($averages['values']['n1'], 0); ?><small>/cm<sup>3</sup></small>
        <?php endif ?>
      </td>
      <td></td>
    </tr>
    <?php endif ?>
    <?php if ($averages['values']['pm4'] > 0 || $averages['values']['n4'] > 0): ?>
    <tr>
      <th scope="row">PM<sub>4</sub></th>
      <td>
        <?php if ($averages['values']['pm4'] > 0): ?>
        <?php echo round($averages['values']['pm4'], 0); ?><small>&nbsp;µg/m<sup>3</sup></small>
        <?php endif ?>
      </td>
      <td></td>
      <td>
        <?php if ($averages['values']['n4'] > 0): ?>
        <?php echo round($averages['values']['n4'], 0); ?><small>/cm<sup>3</sup></small>
        <?php endif ?>
      </td>
      <td></td>
    </tr>
    <?php endif ?>
    <tr>
      <td colspan="5" class="weather-measurements">
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
        <?php
          $timeSinceLastUpdateS = time() - $sensors['last_update'];
          if ($timeSinceLastUpdateS > 15 * 60) {
            $lastUpdatedClass="colorRed";
          } else if ($timeSinceLastUpdateS > 10 * 60) {
            $lastUpdatedClass="colorOrange";
          } else {
            $lastUpdatedClass="";
          }
        ?>
        <small class="<?php echo $lastUpdatedClass ?>">
          <?php echo __('Last update') ?>: <?php echo date("Y-m-d H:i:s", $sensors['last_update']); ?>.
          <?php echo __('Readings') ?>: <?php echo $averages['values']['count']; ?>.
        </small>
      </td>
    </tr>
  </tbody>
</table>
<?php endif ?>