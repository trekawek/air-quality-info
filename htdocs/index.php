<?php
require_once('config.php');
require_once('lib/locale.php');
require_once('lib/pollution_levels.php');
require_once('lib/rrd.php');
require_once('lib/themes.php');

session_start();

function l($device, $action, $query_args = array()) {
  $link = '/'.$device['name'];

  if ($action != 'sensors') {
    $link .= '/'.$action;
  }
  
  $query_arg_added = false;
  foreach ($query_args as $k => $v) {
    if ($query_arg_added) {
      $link .= '&';
    } else {
      $link .= '?';
      $query_arg_added = true;
    }
    $link .= "${k}=${v}";
  }

  if ($link == '') {
    $link = '/';
  }

  return $link;
}

list($uri) = explode('?', $_SERVER['REQUEST_URI']);
$uri = explode('/', $uri);
$uri = array_values(array_filter($uri));

$device = null;
if (count($uri) > 0) {
  foreach (CONFIG['devices'] as $d) {
    if ($uri[0] == $d['name']) {
        $device = $d;
        array_shift($uri);
        break;
    }
  }
}

if (count($uri) > 0) {
  $current_action = array_shift($uri);
} else {
  $current_action = 'sensors';
}

if ($device == null) {
  header('Location: '
    .l(CONFIG['devices'][0], $current_action)
    .($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
  exit;
}

switch ($current_action) {
  case 'update':
  require('api/update.php');
  break;

  case 'migrate_rrd':
  require('api/migrate_rrd.php');
  break;

  case 'graph_data.json':
  require('api/graph_json.php');
  break;

  case 'about':
  require("views/about_${current_lang}.php");
  break;

  case 'graphs':
  require('views/graphs.php');
  break;

  case 'debug':
  require('views/debug.php');
  break;

  case 'sensors':
  default:
  require('views/sensors.php');
  break;
}
?>