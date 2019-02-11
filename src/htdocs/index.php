<?php
session_start();
date_default_timezone_set('Europe/Warsaw');

require_once('lib/locale.php');
$currentLocale = new AirQualityInfo\Locale();
if (isset($_GET['lang'])) {
  $currentLocale->setLang($_GET['lang']);
}

function __($msg) {
  global $currentLocale;
  return $currentLocale->getMessage($msg);
}

require_once('config.php');
require_once('lib/navigation.php');
require_once('lib/math.php');
require_once('lib/pollution_levels.php');
require_once('lib/themes.php');
require_once('lib/router.php');
require_once('model/dao.php');
require_once('model/dao_factory.php');
require_once('model/updater.php');
require_once('model/migration/madavi_migrator.php');
require_once('model/migration/rrd_to_mysql_migrator.php');

require_once('controllers/abstract_controller.php');
require_once('controllers/main_controller.php');
require_once('controllers/graph_controller.php');
require_once('controllers/debug_controller.php');
require_once('controllers/update_controller.php');
require_once('controllers/tool_controller.php');
require_once('controllers/static_controller.php');
require_once('controllers/annual_stats_controller.php');

$dao = create_dao();
$updater = new Updater($dao);
$controllers = array();
$controllers['main'] = new MainController($dao, $currentLocale);
$controllers['graph'] = new GraphController($dao);
$controllers['debug'] = new DebugController($dao);
$controllers['update'] = new UpdateController($dao, $updater);
$controllers['tool'] = new ToolController($dao, $updater);
$controllers['static'] = new StaticController($currentLocale);
$controllers['annual_stats'] = new AnnualStatsController($dao);


$routes = array(
  'GET /[:device]'               => array('main', 'index'),
  'GET /[:device]/main_inner'    => array('main', 'index_inner'),
  'GET /:device/annual_stats'                  => array('annual_stats', 'index'),
  'GET /:device/annual_stats/graph_data.json'  => array('annual_stats', 'get_data'),
  'GET /all/:groupId'            => array('main', 'all'),
  'GET /:device/about'           => array('static', 'about'),
  'GET /offline'                 => array('static', 'offline'),
  'POST /:device/update'         => array('update', 'update'),
  'GET /:device/graphs'          => array('graph', 'index'),
  'GET /[:device]/graph_data.json' => array('graph', 'get_data'),
  'GET /:device/debug'                 => array('debug', 'index'),
  'GET /:device/debug/json'            => array('debug', 'index_json'),
  'GET /:device/debug/json/:timestamp' => array('debug', 'get_json'),
  'GET /:device/tools'                 => array('tool', 'index'),
  'POST /:device/tools/update_rrd_schema' => array('tool', 'update_rrd_schema'),
  'POST /:device/tools/rrd_to_mysql'      => array('tool', 'migrate_rrd_to_mysql'),
  'POST /:device/tools/migrate_madavi'    => array('tool', 'migrate_madavi'),
);

$router = new Router($routes);
list($route, $args) = $router->findRoute(explode("?", $_SERVER['REQUEST_URI'])[0]);
if ($route === null) {
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