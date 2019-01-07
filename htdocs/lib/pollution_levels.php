<?php
// http://powietrze.gios.gov.pl/pjp/current
define('POLLUTION_LEVELS', array(
  array('name' => 'Bardzo niski'),
  array('name' => 'Niski'),
  array('name' => 'Średni'),
  array('name' => 'Wysoki'),
  array('name' => 'Bardzo wysoki'),
));

define('PM10_THRESHOLDS', array(0, 25, 50, 90, 180));
define('PM25_THRESHOLDS', array(0, 15, 30, 55, 110));

define('PM10_LIMIT', 50);
define('PM25_LIMIT', 25);

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