<?php
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

update_rrd($device['esp8266id'], time(), $map['SDS_P2'], $map['SDS_P1'], 'U', 'U', 'U');

if (CONFIG['store_json_payload']) {
  file_put_contents('data/'.$device['esp8266id'].'.json', $payload);
}

echo "OK";
?>