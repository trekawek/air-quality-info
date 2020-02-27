<?php
namespace AirQualityInfo\head;

function navItem($action, $desc, $liClass = "nav-item", $aClass = "nav-link") {
  global $currentAction, $currentController, $uri;
  require('partials/navbar/nav_item.php');
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <?php require("partials/ga.php") ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if (isset($domainTemplate['head_section'])): ?>
<?php echo $domainTemplate['head_section']; ?>
    <?php endif ?>
    <?php if (isset($domainTemplate['customize_favicon']) && $domainTemplate['customize_favicon'] && isset($customBrandIcon)): ?>
    <link rel="shortcut icon" type="image/png" href="<?php echo $customBrandIcon ?>"/>
    <link rel="apple-touch-icon" sizes="512x512" href="<?php echo $customBrandIcon ?>">
    <?php else: ?>
    <link rel="shortcut icon" type="image/png" href="/public/img/aqi-favicon.png"/>
    <link rel="apple-touch-icon" sizes="512x512" href="/public/img/aqi-512.png">
    <?php endif ?>
    <link rel="manifest" href="/manifest.json">

    <?php if (isset($currentDevice) && !(($currentController == 'main' && $currentAction == 'all') || ($currentController == 'map' && $displayLocations))): ?>
    <title><?php echo isset($domainTemplate['brand_name']) ? $domainTemplate['brand_name'] : __('Air quality') ?> - <?php echo $currentDevice['description']; ?></title>
    <?php else: ?>
    <title><?php echo isset($domainTemplate['brand_name']) ? $domainTemplate['brand_name'] : __('Air quality') ?></title>
    <?php endif ?>

    <style><?php echo file_get_contents('public/css/critical.css') ?></style>

    <script defer src="/public/js/vendor.min.js?v=33"></script>
    <script defer src="/public/js/main.js?v=33"></script>
    <script defer src="/public/js/graph.js?v=37"></script>
    <script defer src="/public/js/annual_graph.js?v=32"></script>
  </head>
  <body class="<?php echo $currentTheme->getTheme() ?>" data-pm10-limit1h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_1H ?>" data-pm25-limit1h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_1H ?>" data-pm10-limit24h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_24H ?>" data-pm25-limit24h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_24H ?>" data-current-lang='<?php echo $currentLocale->getCurrentLang() ?>' data-locale='<?php echo json_encode($currentLocale->getJsMessages()) ?>' data-timezone='<?php echo date_default_timezone_get() ?>'>
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <nav class="navbar navbar-expand-md navbar-light bg-light">
<?php if (isset($domainTemplate['brand_name']) || isset($customBrandIcon)): ?>
            <a href="/" class="navbar-left navbar-brand">
              <img src="<?php echo isset($customBrandIcon) ? $customBrandIcon : '/public/img/aqi.png' ?>"/><?php echo isset($domainTemplate['brand_name']) ? $domainTemplate['brand_name'] : '' ?></a>
<?php else: ?>
            <a href="//aqi.eco" class="navbar-left navbar-brand aqieco">
                <img src="/public/img/aqi.png">aqi.eco
            </a>
<?php endif ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Nawigacja">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
                <?php navItem("/", 'Home'); ?>
                <?php if (!(($currentController == 'main' && $currentAction == 'all') || ($currentController == 'map' && $displayLocations))): ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo __('Graphs') ?>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <?php navItem(array('graph', 'index'), 'Graphs', '', 'dropdown-item'); ?>
                    <?php navItem(array('annual_stats', 'index'), 'Annual stats', '', 'dropdown-item'); ?>
                  </ul>
                </li>
                <?php endif ?>
                <?php if ($displayMap): ?>
                    <?php navItem(array('map', 'index'), 'Map'); ?>
                <?php endif ?>
                <?php require('partials/navbar/locations.php') ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo __('Theme') ?>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <li>
                  <?php foreach(\AirQualityInfo\Lib\Theme::THEMES as $name => $desc): ?>
                    <a class="dropdown-item <?php echo ($name == $currentTheme->getTheme()) ? 'active' : ''; ?>" href="<?php echo explode('?', $_SERVER['REQUEST_URI'])[0] . "?theme=$name" ?>"><?php echo __($desc) ?></a>
                  <?php endforeach ?>
                  </li>
                  </ul>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-globe" aria-hidden="true"></i>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <?php foreach(\AirQualityInfo\Lib\Locale::SUPPORTED_LANGUAGES as $lang => $desc): ?>
                  <li>
                    <a class="dropdown-item <?php echo ($lang == $currentLocale->getCurrentLang()) ? 'active' : ''; ?>" href="<?php echo $currentLocale->updateLangPrefix($_SERVER['REQUEST_URI'], $lang) ?>"><img src="/public/img/flags/<?php echo \AirQualityInfo\Lib\Locale::LANG_TO_FLAG[$lang] ?>"/> <?php echo $desc ?></a>
                  </li>
                  <?php endforeach ?>
                  </ul>
                </li>
<?php if (isset($domainTemplate['custom_page_name'])): ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo l('static', 'about') ?>" >
                    <?php echo $domainTemplate['custom_page_name'] ?>
                  </a>
                </li>
<?php endif ?>
              </ul>
            </div>
          </nav>
        </div>
      </div>
<?php if (isset($device['maintenance'])): ?>
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="alert alert-warning">
            <?php echo $device['maintenance'] ?>
          </div>
        </div>
      </div>
<?php endif ?>

<?php if (isset($domainTemplate['header']) && $displayCustomHeader): ?>
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <?php echo $domainTemplate['header'] ?>
        </div>
      </div>
<?php endif ?>