<?php
// http://powietrze.gios.gov.pl/pjp/current
define('POLLUTION_LEVELS', array(
  array('name' => __('Very low')),
  array('name' => __('Low')),
  array('name' => __('Medium')),
  array('name' => __('High')),
  array('name' => __('Very high')),
));

define('PM10_THRESHOLDS_1H',  array(0, 25, 50, 90, 180));
define('PM10_THRESHOLDS_24H', array(0, 15, 30, 50, 100));

define('PM25_THRESHOLDS_1H',  array(0, 15, 30, 55, 110));
define('PM25_THRESHOLDS_24H', array(0, 10, 20, 30, 60));

define('PM10_LIMIT_1H', 50);
define('PM25_LIMIT_1H', 25);

define('PM10_LIMIT_24H', 50);
define('PM25_LIMIT_24H', 25);

function find_level($thresholds, $value) {
  if ($value === null) {
    return null;
  }
  foreach ($thresholds as $i => $v) {
    if ($v > $value) {
      return $i - 1;
    }
  }
  return count($thresholds) - 1;
}

?>