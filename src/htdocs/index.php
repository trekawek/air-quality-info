<?php
namespace AirQualityInfo;

session_start();
date_default_timezone_set('Europe/Warsaw');
require_once('config.php');

$mysqli = new \mysqli(CONFIG['db']['host'], CONFIG['db']['user'], CONFIG['db']['password'], CONFIG['db']['name']);

function camelize($string) {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
}

function decamelize($string) {
    return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
}

function noRouteFound() {
    //header("Location: //" . CONFIG['admin_domains'][0].'/');
    die();
}

function getParamValue($param, &$bindings) {
    $paramName = $param->getName();
    if (!isset($bindings[$paramName])) {
        if ($param->hasType()) {
            $value = injectClass($param->getType()->__toString(), $bindings);
            $bindings[$paramName] = $value;
        } else {
            throw new \Exception("Can't find type for the $paramName in $className");
        }
    }
    return $bindings[$paramName];
}

function injectClass($className, &$bindings) {
    if (!class_exists($className, true)) {
        throw new \Exception("Invalid class: $className");
    }
    $class = new \ReflectionClass($className);
    $constructor = $class->getConstructor();
    $constructorArgs = array();
    foreach($constructor->getParameters() as $param) {
        $constructorArgs[] = getParamValue($param, $bindings);
    }

    $object = new $className(...$constructorArgs);
    
    foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
        $methodName = $method->getName();
        if ($method->getNumberOfParameters() === 1 && preg_match('/^set[A-Z]/', $methodName)) {
            $param = $method->getParameters()[0];
            $value = getParamValue($param, $bindings);
            $object->$methodName($value);
        }
    }

    return $object;
}

spl_autoload_register(function($className) {
    $classPath = explode('\\', $className);
    if (array_shift ($classPath) != 'AirQualityInfo') {
        return;
    }
    $classPath = array_map (function($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }, $classPath);

    $path = implode('/', $classPath).".php";
    if (file_exists($path)) {
        require_once($path);
    } else {
        throw new \Exception("Can't load file $path for class $className");
    }
});

$currentLocale = new Lib\Locale();
if (isset($_GET['lang'])) {
    $currentLocale->setLang($_GET['lang']);
}

$currentTheme = new Lib\Theme();
if (isset($_GET['theme'])) {
    $currentTheme->setTheme($_GET['theme']);
}

$host = explode(':', $_SERVER['HTTP_HOST'])[0];
if (in_array($host, CONFIG['admin_domains'])) {
    // handle the main domain
} else {
    $userModel = new model\UserModel($mysqli);
    foreach (CONFIG['user_domain_suffixes'] as $suffix) {
        if (substr($host, -strlen($suffix)) === $suffix) {
            $host = substr($host, 0, -strlen($suffix));
            $userId = $userModel->getIdByDomain($host);
        }
    }

    if ($userId === null) {
        noRouteFound();
    }

    $devices = (new model\DeviceModel($mysqli))->getDevicesForUser($userId);
    if (count($devices) === 0) {
        noRouteFound();
    }

    $routes = array(
        'GET /:device'               => array('main', 'index'),
        'GET /:device/main_inner'    => array('main', 'index_inner'),
        'GET /:device/annual_stats'    => array('annual_stats', 'index'),
        'GET /:device/annual_stats/graph_data.json'  => array('annual_stats', 'get_data'),
        'GET /all'                     => array('main', 'all'),
        'GET /:device/about'           => array('static', 'about'),
        'GET /offline'                 => array('static', 'offline'),
        'POST /update'                 => array('update', 'update'),
        'GET /:device/graphs'          => array('graph', 'index'),
        'GET /:device/graph_data.json' => array('graph', 'get_data')
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
    $bindings = array(
        'templateVariables' => array(
            'currentController' => $currentController,
            'currentAction' => $currentAction,
            'currentLocale' => $currentLocale,
            'currentTheme' => $currentTheme,
            'currentDevice' => $currentDevice,
            'devices' => $devices
        ),
        'devices' => $devices,
        'mysqli' => $mysqli,
        'currentLocale' => $currentLocale
    );

    injectClass('\\AirQualityInfo\\Controller\\'.camelize($currentController).'Controller', $bindings)->$currentAction(...array_values($args));
}
?>