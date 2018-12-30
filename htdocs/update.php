<?php
require('config.php');

if (!($_SERVER['PHP_AUTH_USER'] == $user && $_SERVER['PHP_AUTH_PW'] == $pass)) {
  header('WWW-Authenticate: Basic realm="data.php"');
  header('HTTP/1.0 401 Unauthorized');
  exit;
}

function create($rrd_file) {
  rrd_create($rrd_file, array(
    '--start=now-2h',
    '--step=3m',
    'DS:PM25:GAUGE:5m:0:1000',
    'DS:PM10:GAUGE:5m:0:1000',
    'DS:TEMPERATURE:GAUGE:5m:-100:100',
    'DS:PRESSURE:GAUGE:5m:900:1100',
    'DS:HUMIDITY:GAUGE:5m:0:1100',
    'RRA:AVERAGE:0.5:3m:24h',
    'RRA:AVERAGE:0.5:15m:7d',
    'RRA:AVERAGE:0.5:1h:30d',
    'RRA:AVERAGE:0.5:12h:1y'
  ));
}

function update($rrd_file, $time, $pm25, $pm10, $temp, $press, $hum) {
  rrd_update($rrd_file, array("${time}:${pm25}:${pm10}:${temp}:${press}:${hum}"));
}

$payload = file_get_contents("php://input");
$data = json_decode($payload, true);
$sensors = $data['sensordatavalues'];

$map = array();
foreach ($sensors as $row) {
  $map[$row['value_type']] = $row['value'];
}

if (!file_exists($rrd_file)) {
  create($rrd_file);
}

update($rrd_file, time(), $map['SDS_P2'], $map['SDS_P1'], 'U', 'U', 'U');
echo "OK";
?>