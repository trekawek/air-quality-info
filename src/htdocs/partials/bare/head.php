<!DOCTYPE html>
<html lang="<?php echo $currentLocale->getCurrentLang(); ?>">
  <head>
    <?php require("partials/ga.php") ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style><?php echo file_get_contents('public/css/critical.css') ?></style>

    <?php echo jsLink("public/js/vendor.min.js", "defer"); ?>
    <?php echo jsLink("public/js/main.js", "defer"); ?>
  </head>
  <body class="<?php echo $currentTheme->getTheme() ?>" data-pm10-limit1h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_1H ?>" data-pm25-limit1h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_1H ?>" data-pm10-limit24h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_24H ?>" data-pm25-limit24h="<?php echo \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_24H ?>" data-current-lang='<?php echo $currentLocale->getCurrentLang() ?>' data-locale='<?php echo json_encode($currentLocale->getJsMessages()) ?>' data-timezone='<?php echo date_default_timezone_get() ?>'>
