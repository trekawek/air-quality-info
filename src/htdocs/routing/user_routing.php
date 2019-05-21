<?php
namespace AirQualityInfo;

$userModel = new model\UserModel($mysqli);
foreach (CONFIG['user_domain_suffixes'] as $suffix) {
    if (substr($host, -strlen($suffix)) === $suffix) {
        $host = substr($host, 0, -strlen($suffix));
        $userId = $userModel->getIdByDomain($host);
    }
}

if ($userId === null) {
    Lib\Router::send404();
}

$devices = (new model\DeviceModel($mysqli))->getDevicesForUser($userId);
if (count($devices) === 0) {
    Lib\Router::send404();
}

$routes = array(
    'GET /[:device]'                 => array('main', 'index'),
    'GET /[:device]/main_inner'      => array('main', 'index_inner'),
    'GET /:device/annual_stats'      => array('annual_stats', 'index'),
    'GET /:device/annual_stats/graph_data.json'  => array('annual_stats', 'get_data'),
    'GET /all'                       => array('main', 'all'),
    'GET /:device/about'             => array('static', 'about'),
    'GET /offline'                   => array('static', 'offline'),
    'POST /update'                   => array('update', 'update'),
    'GET /:device/graphs'            => array('graph', 'index'),
    'GET /[:device]/graph_data.json' => array('graph', 'get_data')
);

$router = new Lib\Router($routes, $devices);
list($route, $args) = $router->findRoute(
    $_SERVER['REQUEST_METHOD'],
    explode("?", $_SERVER['REQUEST_URI'])[0]
);

// the domain is correct, but the path is not
if ($route === null) {
    header("Location: /" . $devices[0]['name']);
    die();
}

if (isset($args['device'])) {
    $currentDevice = $args['device'];
} else {
    $currentDevice = $devices[0];
}

$currentController = $route[0];
$currentAction = $route[1];

$currentTheme = new Lib\Theme();
if (isset($_GET['theme'])) {
    $currentTheme->setTheme($_GET['theme']);
}

$templateVariables = array(
    'currentController' => $currentController,
    'currentAction' => $currentAction,
    'currentLocale' => $currentLocale,
    'currentTheme' => $currentTheme,
    'currentDevice' => $currentDevice,
    'devices' => $devices
);
$diContainer->addBindings($templateVariables);
$diContainer->setBinding('templateVariables', $templateVariables);
$diContainer->setBinding('mysqli', $mysqli);

$diContainer->injectClass('\\AirQualityInfo\\Controller\\'.Lib\StringUtils::camelize($currentController).'Controller')->$currentAction(...array_values($args));

?>