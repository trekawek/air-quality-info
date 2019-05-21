<?php
namespace AirQualityInfo;

session_start();
date_default_timezone_set('Europe/Warsaw');
require_once('config.php');

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

$mysqli = new \mysqli(CONFIG['db']['host'], CONFIG['db']['user'], CONFIG['db']['password'], CONFIG['db']['name']);
$diContainer = new Lib\DiContainer();

$currentLocale = new Lib\Locale();
if (isset($_GET['lang'])) {
    $currentLocale->setLang($_GET['lang']);
}

?>