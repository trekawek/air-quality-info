<?php
namespace AirQualityInfo;

\AirQualityInfo\Lib\HttpUtils::setACAOHeader();

$userModel = $diContainer->injectClass('\\AirQualityInfo\\Model\\UserModel');
$userId = $userModel->parseFqdn($host);
if ($userId === null) {
    Lib\Router::send404();
}

$user = $userModel->getUserById($userId);
date_default_timezone_set($user['timezone']);

$deviceModel = $diContainer->injectClass('\\AirQualityInfo\\Model\\DeviceModel');
$devices = $deviceModel->getAllUserDevices($userId);
if (count($devices) === 0) {
    Lib\Router::send404();
}
$deviceHierarchyModel = $diContainer->injectClass('\\AirQualityInfo\\Model\\DeviceHierarchyModel');
foreach ($devices as $i => $d) {
    $paths = $deviceHierarchyModel->getDevicePaths($userId, $d['id']);
    if (!empty($paths)) {
        $devices[$i]['path'] = $paths[0];
    } else {
        $devices[$i]['path'] = null;
    }
}

$routes = array(
    'GET /:lang/[:device]'                 => array('main', 'index'),
    'GET /[:lang]/:device/data.json'       => array('main', 'data_json'),
    'GET /:lang/[:device]/main_inner'      => array('main', 'index_inner'),
    'GET /:lang/:device/annual_stats'      => array('annual_stats', 'index'),
    'GET /:lang/:device/annual_stats/graph_data.json'  => array('annual_stats', 'get_data'),
    'GET /:lang/all/[:node_id]'            => array('main', 'all'),
    'GET /:lang/offline'                   => array('static', 'offline'),
    'GET /:lang/about'                     => array('static', 'about'),
    'GET /:lang/:device/graphs'            => array('graph', 'index'),
    'GET /:lang/[:device]/graph_data.json' => array('graph', 'get_data'),
    'GET /:lang/map'                       => array('map', 'index'),
    'GET /:lang/map/data.json'             => array('map', 'data'),
    'GET /:lang/map/:device'               => array('map', 'sensorInfo'),
    'GET /:lang/attachment/:name'          => array('attachment', 'get'),
    'GET /:lang/:device/widget'            => array('device_widget', 'show'),
    'GET /:lang/widget/:widgetId'          => array('domain_widget', 'show'),

    // deprecated
    'POST /u/:key'                         => array('update', 'updateWithKey'),
    'POST /update'                         => array('update', 'update'),
);

$router = new Lib\Router($routes, $currentLocale, $devices, $user);

$uri = urldecode(explode("?", $_SERVER['REQUEST_URI'])[0]);
list($route, $args) = $router->findRoute(
    $_SERVER['REQUEST_METHOD'],
    $uri
);

if ($route === null && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $uriWithLang = $currentLocale->addLangPrefix($uri);
    if ($uri != $uriWithLang && $router->findRoute('GET', $uriWithLang)) {
        header("Location: ".$currentLocale->addLangPrefix($_SERVER['REQUEST_URI']));
        exit;
    }
}

if ($route === null) {
    header("Location: /");
    die();
}

if (isset($args['lang'])) {
    $currentLocale->setLang($args['lang']);
    unset($args['lang']);
}

if (isset(\AirQualityInfo\Lib\Locale::SUPPORTED_LANGUAGES[substr($uri, 1)]) && $user['redirect_root']) {
    $redirectedUri = $user['redirect_root'];
    $result = $router->findRoute('GET', $redirectedUri);
    if ($result[0] != null) {
        $route = $result[0];
        $args = $result[1];
    } else {
        $redirectedUri = $currentLocale->addLangPrefix($redirectedUri);
        $result = $router->findRoute('GET', $redirectedUri);
        if ($result[0] != null) {
            $route = $result[0];
            $args = $result[1];
        }
    }
}

if (isset($args['lang'])) {
    $currentLocale->setLang($args['lang']);
    unset($args['lang']);
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
    'currentTheme' => $currentTheme,
    'currentDevice' => $currentDevice,
    'currentLocale' => $currentLocale,
    'uri' => $uri,
    'devices' => $devices
);
$diContainer->addBindings($templateVariables);
$diContainer->setBinding('templateVariables', $templateVariables);
$diContainer->setBinding('userId', $userId);
$diContainer->setBinding('user', $user);
$diContainer->setBinding('customFqdns', $userModel->getCustomFqdns($userId));

$diContainer->injectClass('\\AirQualityInfo\\Controller\\'.Lib\StringUtils::camelize($currentController).'Controller')->$currentAction(...array_values($args));

?>