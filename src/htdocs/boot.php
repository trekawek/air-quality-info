<?php
namespace AirQualityInfo;

require_once("vendor/autoload.php");

session_start();
date_default_timezone_set('Europe/Warsaw');

$configFile = getenv('AQI_CONFIG');
if ($configFile === null) {
    $configFile = 'config.php';
}
require_once($configFile);

spl_autoload_register(function($className) {
    $rootPath = getenv('AQI_PATH');
    $classPath = explode('\\', $className);
    if (array_shift ($classPath) != 'AirQualityInfo') {
        return;
    }
    $classPath = array_map (function($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }, $classPath);
    $path = $rootPath."/".implode('/', $classPath).".php";
    if (file_exists($path)) {
        require_once($path);
    } else {
        throw new \Exception("Can't load file $path for class $className");
    }
});

$dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8", CONFIG['db']['host'], CONFIG['db']['name']);
$options = [
  \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
];
$pdo = new \PDO($dsn, CONFIG['db']['user'], CONFIG['db']['password'], $options);

$space = new \SpacesConnect(CONFIG['spaces']['key'], CONFIG['spaces']['secret'], CONFIG['spaces']['space'], CONFIG['spaces']['region']);

$diContainer = new Lib\DiContainer();

$currentLocale = new Lib\Locale();
if (isset($_GET['lang'])) {
    $currentLocale->setLang($_GET['lang']);
}

$diContainer->setBinding('pdo', $pdo);
$diContainer->setBinding('space', $space);
$diContainer->setBinding('currentLocale', $currentLocale);

Lib\CsrfToken::generateTokenIfNotExists();
?>
