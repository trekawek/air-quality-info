<!DOCTYPE html>
<html lang="en">
  <head>
<?php if (CONFIG['ga_id']): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo CONFIG['ga_id']; ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '<?php echo CONFIG['ga_id']; ?>');
    </script>
<?php endif; ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta http-equiv="refresh" content="180" >
    <link rel="apple-touch-icon" href="/public/img/dragon_white_background.png">
    <link rel="icon" type="image/png" href="/public/img/dragon.png">

    <title><?php echo __('Air quality') ?> - <?php echo $device['description']; ?></title>

    <link rel="stylesheet" href="/public/css/themes/<?php echo $current_theme ?>.min.css"/>
    <link rel="stylesheet" href="/public/css/vendor.min.css"/>
    <link rel="stylesheet" href="/public/css/style.css?v=1.3"/>

    <script defer src="/public/js/vendor.min.js"></script>
    <script defer src="/public/js/main.js?v=1.11"></script>
  </head>
  <body data-device-name="<?php echo $device['name'] ?>" data-pm10-limit1h="<?php echo PM10_LIMIT_1H ?>" data-pm25-limit1h="<?php echo PM25_LIMIT_1H ?>" data-pm10-limit24h="<?php echo PM10_LIMIT_24H ?>" data-pm25-limit24h="<?php echo PM25_LIMIT_24H ?>" data-current-lang='<?php echo $current_lang ?>' data-locale='<?php echo json_encode($locale) ?>'>
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <nav class="navbar navbar-expand-md navbar-light bg-light">
            <a href="<?php echo l($device, 'sensors'); ?>" class="navbar-left navbar-brand"><img src="/public/img/dragon.png"/> Air Quality Info</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Nawigacja">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
                <?php foreach(array('sensors' => __('Home'), 'graphs' => __('Graphs'), 'about' => __('About')) as $action => $name): ?>
                <li class="nav-item">
                  <a class="nav-link <?php echo ($action == $current_action) ? 'active' : ''; ?>" href="<?php echo l($device, $action); ?>"><?php echo $name; ?></a>
                </li>
                <?php endforeach ?>
                <?php require('partials/navbar/locations.php') ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo __('Theme') ?>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <li>
                  <?php foreach(THEMES as $name => $desc): ?>
                    <a class="dropdown-item <?php echo ($name == $current_theme) ? 'active' : ''; ?>" href="<?php echo l($device, $current_action, array('theme' => $name)); ?>"><?php echo $desc ?></a>
                  <?php endforeach ?>
                  </li>
                  </ul>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-globe" aria-hidden="true"></i>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <?php foreach($supported_languages as $lang => $desc): ?>
                  <li>
                    <a class="dropdown-item <?php echo ($lang == $current_lang) ? 'active' : ''; ?>" href="<?php echo l($device, $current_action, array('lang' => $lang)); ?>"><img src="/public/img/flags/<?php echo $lang ?>.png"/> <?php echo $desc ?></a>
                  </li>
                  <?php endforeach ?>
                  </ul>
                </li>
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
