<?php
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
        require_once("../htdocs/$path");
    } else {
        throw new \Exception("Can't load file $path for class $className");
    }
});

$configFile = getenv('AQI_CONFIG');
if ($configFile === null) {
    $configFile = 'config.php';
}
require_once($configFile);
$mysqli = new \mysqli(CONFIG['db']['host'], CONFIG['db']['user'], CONFIG['db']['password'], CONFIG['db']['name']);
?>