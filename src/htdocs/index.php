<?php
session_start();
date_default_timezone_set('Europe/Warsaw');

require_once('config.php');
require_once('lib/locale.php');
require_once('lib/math.php');
require_once('lib/pollution_levels.php');
require_once('lib/sensors.php');
require_once('lib/themes.php');
require_once('lib/routing.php');
require_once('db/dao.php');
require_once('db/dao_factory.php');

$routes = array(
  'index'           => array('include' => 'views/main.php',     'redirect' => false),
  'index_pwa'       => array('include' => 'views/main_pwa.php', 'redirect' => false),
  'sensors'         => array('include' => 'views/sensors.php',  'redirect' => false),
  'graphs'          => array('include' => 'views/graphs.php'),
  'graph_data.json' => array('include' => 'api/graph_json.php', 'redirect' => false),
  'offline'         => array('include' => 'views/offline.php',  'redirect' => false),
  'about'           => array('include' => "views/about_${current_lang}.php"),
  'update'          => array('include' => 'api/update.php', 'authenticate' => true),

  'debug'           => array('include' => 'views/debug.php'),
  'debug/json'      => array('include' => 'views/debug_json.php', 'authenticate' => true),
  
  'tools/update_rrd_schema' => array('include' => 'tools/update_rrd_schema.php', 'authenticate' => true),
  'tools/rrd_to_mysql' =>      array('include' => 'tools/rrd_to_mysql.php',      'authenticate' => true),
);

list($device, $current_action) = parse_uri();
if ($current_action === null) {
  $current_action = array_keys($routes)[0];
  if ($device === null) {
    $device = CONFIG['devices'][0];
  }
}
$route = get_route($routes, $current_action);
if ($device === null) {
  if (!isset($route['redirect']) || $route['redirect'] === true) {
    header("Location: /"
      .CONFIG['devices'][0]['name']
      .'/'.$current_action
      .($_SERVER['QUERY_STRING'] === '' ? '' : '?'.$_SERVER['QUERY_STRING']));
    exit;
  } else {
    $device = CONFIG['devices'][0];
  }
}
$dao = create_dao($device);
if ($route['authenticate']) {
  authenticate($device);
}
require($route['include']);
?>