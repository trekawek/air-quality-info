<?php
function read_value($device, $value_name, $sensor_values, $undefined_value = null) {
  $value = null;
  if (isset($device['value_mapping'][$value_name])) {
    $mapped_name = $device['value_mapping'][$value_name];
    if ($mapped_name && isset($sensor_values[$mapped_name])) {
      $value = $sensor_values[$mapped_name];
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

update_rrd(
  $device['esp8266id'],
  $time,
  read_value($device, 'pm25', $map, 'U'),
  read_value($device, 'pm10', $map, 'U'),
  read_value($device, 'temperature', $map, 'U'),
  read_value($device, 'pressure', $map, 'U') / 100,
  read_value($device, 'humidity', $map, 'U')
);

if (CONFIG['store_json_payload']) {
  file_put_contents('data/'.$device['esp8266id'].'.json', $payload);
}

echo "OK";
?>