<?php
require_once('config.php');
require_once('lib/pollution_levels.php');
require_once('lib/rrd.php');
require_once('lib/utils.php');

list ($uri, $query) = explode('?', $_SERVER['REQUEST_URI']);
$uri = explode('/', $uri);
$uri = array_values(array_filter($uri));

$device = CONFIG['devices'][0];
foreach (CONFIG['devices'] as $d) {
  if ($uri[0] == $d['name']) {
    $device = $d;
    array_shift($uri);
    break;
  }
}

$current_action = $uri[0];
switch ($current_action) {
  case 'update':
  require('update.php');
  break;

  case 'graphs':
  require('views/graph_all.php');
  break;

  case 'graph.png':
  require('views/graph_img.php');
  break;

  case 'sensors':
  default:
  $current_action = 'sensors';
  require('views/sensors.php');
  break;
}
?>