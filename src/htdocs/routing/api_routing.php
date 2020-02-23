<?php
namespace AirQualityInfo;

\AirQualityInfo\Lib\HttpUtils::setACAOHeader();

$routes = array(
    'POST /update/:key' => array('update', 'updateWithKey'),
);

$router = new Lib\Router($routes, $currentLocale);

$uri = urldecode(explode("?", $_SERVER['REQUEST_URI'])[0]);
list($route, $args) = $router->findRoute(
    $_SERVER['REQUEST_METHOD'],
    $uri
);

if ($route === null) {
    http_response_code(404);
    die();
}

$currentController = $route[0];
$currentAction = $route[1];

$diContainer->injectClass('\\AirQualityInfo\\Api\\Controller\\'.Lib\StringUtils::camelize($currentController).'Controller')->$currentAction(...array_values($args));

?>