<?php
namespace AirQualityInfo;

Lib\CsrfToken::verifyToken($_POST['_csrf_token']);

$routes = array(
    'GET /login' => array('user', 'login'),
    'POST /login' => array('user', 'doLogin'),
    'GET /logout' => array('user', 'logout'),
    'GET /register' => array('user', 'register'),
    'POST /register' => array('user', 'doRegister'),

    'GET /' => array('device', 'index'),
    'GET /device/create' => array('device', 'create'),
    'POST /device/create' => array('device', 'create'),
    'GET /device/:device_id' => array('device', 'edit'),
    'POST /device/:device_id' => array('device', 'update'),
    'DELETE /device/:device_id' => array('device', 'deleteDevice'),

    'POST /device/:device_id/mapping' => array('device', 'createMapping'),
    'DELETE /device/:device_id/mapping/:mapping_id' => array('device', 'deleteMapping'),
);

$router = new Lib\Router($routes, $devices);
list($route, $args) = $router->findRoute(
    $_SERVER['REQUEST_METHOD'],
    explode("?", $_SERVER['REQUEST_URI'])[0]
);

if ($route === null) {
    Lib\Router::send404();
}

$currentController = $route[0];
$currentAction = $route[1];

$templateVariables = array(
    'currentController' => $currentController,
    'currentAction' => $currentAction,
    'currentLocale' => $currentLocale
);
$diContainer->addBindings($templateVariables);
$diContainer->setBinding('templateVariables', $templateVariables);
$diContainer->setBinding('mysqli', $mysqli);

$controller = $diContainer->injectClass('\\AirQualityInfo\\Admin\\Controller\\'.Lib\StringUtils::camelize($currentController).'Controller');
$controller->beforeAction();
$controller->$currentAction(...array_values($args));

?>