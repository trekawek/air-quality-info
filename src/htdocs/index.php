<?php
session_start();
date_default_timezone_set('Europe/Warsaw');

require_once('lib/locale.php');
require_once('config.php');
require_once('lib/navigation.php');
require_once('lib/math.php');
require_once('lib/pollution_levels.php');
require_once('lib/themes.php');
require_once('lib/router.php');
require_once('db/dao.php');
require_once('db/dao_factory.php');

require_once('controllers/abstract_controller.php');
require_once('controllers/main_controller.php');
require_once('controllers/graph_controller.php');
require_once('controllers/debug_controller.php');
require_once('controllers/update_controller.php');
require_once('controllers/tool_controller.php');
require_once('controllers/static_controller.php');

$dao = create_dao($device);
$controllers = array(
  'main' => new MainController($dao, $currentLocale),
  'graph' => new GraphController($dao),
  'debug' => new DebugController($dao),
  'update' => new UpdateController($dao),
  'tool' => new ToolController($dao),
  'static' => new StaticController($currentLocale),
);

$routes = array(
  '/[:device]'               => array('main', 'index'),
  '/[:device]/main_inner'    => array('main', 'index_inner'),
  '/all/:groupId'            => array('main', 'all'),
  '/:device/about'           => array('static', 'about'),
  '/offline'                 => array('static', 'offline'),
  '/:device/update'          => array('update', 'update'),
  '/:device/graphs'          => array('graph', 'index'),
  '/[:device]/graph_data.json' => array('graph', 'get_data'),
  '/:device/debug'                 => array('debug', 'index'),
  '/:device/debug/json'            => array('debug', 'index_json'),
  '/:device/debug/json/:timestamp' => array('debug', 'get_json'),
  '/:device/tools/update_rrd_schema' => array('tool', 'update_rrd_schema'),
  '/:device/tools/rrd_to_mysql'      => array('tool', 'rrd_to_mysql')
);

$router = new Router($routes);
list($path, $route, $args) = $router->findRoute(explode("?", $_SERVER['REQUEST_URI'])[0]);
if ($path === null) {
  header("Location: /");
  die();
}

if (isset($args['device'])) {
  $currentDevice = $args['device'];
} else {
  $currentDevice = CONFIG['devices'][0];
}
$currentController = $route[0];
$currentAction = $route[1];

call_user_func_array(array($controllers[$currentController], $currentAction), array_values($args));
?>