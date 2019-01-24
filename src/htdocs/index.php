<?php
session_start();
date_default_timezone_set('Europe/Warsaw');

require_once('config.php');
require_once('lib/locale.php');
require_once('lib/math.php');
require_once('lib/pollution_levels.php');
require_once('lib/themes.php');
require_once('lib/routing.php');
require_once('db/dao.php');
require_once('db/dao_factory.php');

list($device, $current_action) = parse_uri();
$dao = create_dao($device);

$routes = array(
  'sensors'         => array('include' => 'views/sensors.php'),
  'graphs'          => array('include' => 'views/graphs.php'),
  'graph_data.json' => array('include' => 'api/graph_json.php'),
  'about'           => array('include' => "views/about_${current_lang}.php"),
  'update'          => array('include' => 'api/update.php', 'authenticate' => true),

  'debug'           => array('include' => 'views/debug.php'),
  'debug/json'      => array('include' => 'views/debug_json.php', 'authenticate' => true),

  'tools/update_rrd_schema' => array('include' => 'tools/update_rrd_schema.php', 'authenticate' => true),
  'tools/rrd_to_mysql' =>      array('include' => 'tools/rrd_to_mysql.php',      'authenticate' => true),
);

$route = get_route($routes, $current_action);
if ($route === null) {
  $route = $routes['sensors'];
}
if ($route['authenticate']) {
  authenticate($device);
}
require($route['include']);
?>