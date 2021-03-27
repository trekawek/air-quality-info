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
require_once('lib/template_utils.php');

spl_autoload_register(function($className) {
    $rootPath = getenv('AQI_PATH');
    $classPath = explode('\\', $className);
    if (array_shift ($classPath) != 'AirQualityInfo') {
        return;
    }
    $classPath = array_map(function($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }, $classPath);
    $path = $rootPath."/".implode('/', $classPath).".php";
    if (file_exists($path)) {
        require_once($path);
    } else {
        throw new \Exception("Can't load file $path for class $className");
    }
});

$diContainer = new Lib\DiContainer();

$currentLocale = new Lib\Locale();

$diContainer->setBinding('currentLocale', $currentLocale);

$diContainer->setLazyBinding('pdo', function() {
    $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8", CONFIG['db']['host'], CONFIG['db']['name']);
    $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
    ];
    return new \PDO($dsn, CONFIG['db']['user'], CONFIG['db']['password'], $options);
});
$diContainer->setBinding('s3Bucket', CONFIG['s3Bucket']);
$diContainer->setLazyBinding('s3Client', function() {
    return \Aws\S3\S3Client::factory(CONFIG['s3']);
});
$diContainer->setLazyBinding('beanstalk', function() {
    return \Pheanstalk\Pheanstalk::create('beanstalkd');
});
$diContainer->setLazyBinding('cache', function() {
    if (!file_exists(CONFIG['cache_root'])) {
        mkdir(CONFIG['cache_root'], 0777, true);
    }
    $cacheStorage = new \Nette\Caching\Storages\FileStorage(CONFIG['cache_root']);
    return new \Nette\Caching\Cache($cacheStorage);
});
$diContainer->setLazyBinding('mailgun', function() {
    if (!isset(CONFIG['mailgun_key']) || !CONFIG['mailgun_key']) {
        return null;
    } else {
        return \Mailgun\Mailgun::create(CONFIG['mailgun_key'], 'https://api.eu.mailgun.net');
    }
});

Lib\CsrfToken::generateTokenIfNotExists();
?>
