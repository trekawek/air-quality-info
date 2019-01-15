<?php

require_once($_ENV['HTDOCS'].'/db/dao.php');
require_once($_ENV['HTDOCS'].'/db/mysql.php');

$rrd_file = $argv[1];
$esp8266id = basename($rrd_file, '.rrd');

$mysqli = new mysqli($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE']);
$dao = new MysqlDao($esp8266id, $mysqli);
/*
$result = rrd_fetch($rrd_file, array("AVERAGE", "--start=now-30d"));
$data = $result['data'];
foreach (array_keys($data['PM25']) as $ts) {
    if ($data['PM25'][$ts] === null) {
        continue;
    }
    $dao->update($ts, $data['PM25'][$ts], $data['PM10'][$ts], $data['TEMPERATURE'][$ts], $data['PRESSURE'][$ts], $data['HUMIDITY'][$ts], $data['HEATER_TEMPERATURE'][$ts], $data['HEATER_HUMIDITY'][$ts]);
}*/

$result = rrd_fetch($rrd_file, array("AVERAGE"));
$data = $result['data'];
foreach (array_keys($data['PM25']) as $ts) {
    if ($data['PM25'][$ts] === null) {
        continue;
    }
    $dao->update($ts, $data['PM25'][$ts], $data['PM10'][$ts], $data['TEMPERATURE'][$ts], $data['PRESSURE'][$ts], $data['HUMIDITY'][$ts], $data['HEATER_TEMPERATURE'][$ts], $data['HEATER_HUMIDITY'][$ts]);
}

?>