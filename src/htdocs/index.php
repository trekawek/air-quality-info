<?php
session_start();

require_once('config.php');
require_once('lib/locale.php');
require_once('lib/math.php');
require_once('lib/pollution_levels.php');
require_once('lib/themes.php');
require_once('db/dao.php');

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

function authenticate($device) {
  if (!($_SERVER['PHP_AUTH_USER'] == $device['user'] && $_SERVER['PHP_AUTH_PW'] == $device['password'])) {
    header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
  }  
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

switch (CONFIG['db']['type']) {
  case 'mysql':
  require_once('db/mysql.php');
  $mysqli = new mysqli(CONFIG['db']['host'], CONFIG['db']['user'], CONFIG['db']['password'], CONFIG['db']['name']);
  $dao = new MysqlDao($device['esp8266id'], $mysqli);
  break;

  default:
  case 'rrd':
  require_once('db/rrd.php');
  $dao = new RRRDao($device['esp8266id']);
  break;
}

if ($current_action == 'tools') {
  authenticate($device);
  switch ($uri[0]) {
    case 'update_rrd_schema':
    require('tools/update_rrd_schema.php');
    break;

    case 'rrd_to_mysql':
    require('tools/rrd_to_mysql.php');
    break;
  }
} else {
  switch ($current_action) {
    case 'update':
    authenticate($device);
    require('api/update.php');
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
}
?>