<?php
if (!($_SERVER['PHP_AUTH_USER'] == $device['user'] && $_SERVER['PHP_AUTH_PW'] == $device['password'])) {
  header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
  header('HTTP/1.0 401 Unauthorized');
  exit;
}

create_rrd($device['esp8266id']);

echo "RRD file migrated";
?>