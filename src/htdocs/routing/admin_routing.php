<?php
namespace AirQualityInfo;

$routes = array(
    'GET /' => array('main', 'index'),
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

$diContainer->injectClass('\\AirQualityInfo\\Admin\\Controller\\'.Lib\StringUtils::camelize($currentController).'Controller')->$currentAction(...array_values($args));

?>