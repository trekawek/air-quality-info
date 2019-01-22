<?php
define('VALUE_MAPPING', array(
  'pm10'        => 'SDS_P1',
  'pm25'        => 'SDS_P2',
  'temperature' => array('BME280_temperature', 'BMP_temperature'),
  'humidity'    => 'BME280_humidity',
  'pressure'    => array('BME280_pressure', 'BMP_pressure'),
  'heater_temperature' => 'temperature',
  'heater_humidity'    => 'humidity',
  'gps_time'    => 'GPS_time',
  'gps_date'    => 'GPS_date',
));

function read_value($device, $value_name, $sensor_values, $undefined_value = null) {
  $value = null;
  if (!isset(VALUE_MAPPING[$value_name])) {
    return $undefined_value;
  }
  $mapped_names = VALUE_MAPPING[$value_name];
  if (!is_array($mapped_names)) {
    $mapped_names = array($mapped_names);
  }
  foreach ($mapped_names as $mapped_name) {
    if (isset($sensor_values[$mapped_name])) {
      $value = $sensor_values[$mapped_name];
      break;
    }
  }
  return $value == null ? $undefined_value : $value;
}

if (!($_SERVER['PHP_AUTH_USER'] == $device['user'] && $_SERVER['PHP_AUTH_PW'] == $device['password'])) {
  header('WWW-Authenticate: Basic realm="data.php"');
  header('HTTP/1.0 401 Unauthorized');
  exit;
}

$payload = file_get_contents("php://input");
$data = json_decode($payload, true);
$sensors = $data['sensordatavalues'];

$map = array();
foreach ($sensors as $row) {
  $map[$row['value_type']] = $row['value'];
}

if ($device['esp8266id'] != $data['esp8266id']) {
  error_log('esp8266id mismatch. Expected: '.$device['esp8266id'].' but got '.$data['esp8266id']);
  exit;
}

$time = time();

$gps_date = read_value($device, 'gps_date', $map, null);
$gps_time = read_value($device, 'gps_time', $map, null);
if ($gps_date && $gps_time) {
  $time = DateTime::createFromFormat('m/d/Y H:i:s.u', $gps_date.' '.$gps_time, new DateTimeZone('UTC'))->getTimestamp();
}

if (CONFIG['store_json_payload']) {
  $dao->logJsonUpdate($time, $payload);
}

$pressure = read_value($device, 'pressure', $map);
if ($pressure !== null) {
  $pressure /= 100;
}

echo $dao->update(
  $time,
  read_value($device, 'pm25', $map),
  read_value($device, 'pm10', $map),
  read_value($device, 'temperature', $map),
  $pressure,
  read_value($device, 'humidity', $map),
  read_value($device, 'heater_temperature', $map),
  read_value($device, 'heater_humidity', $map)
);
?>