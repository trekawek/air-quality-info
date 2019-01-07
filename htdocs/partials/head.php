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

    <title>Jakość powietrza - <?php echo $device['description']; ?></title>

    <!-- Bootstrap -->
    <link href="/public/css/themes/<?php echo $current_theme ?>.min.css" rel="stylesheet">
    <link href="/public/css/style.css?v=1.0" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body data-pm10-limit="<?php echo PM10_LIMIT ?>" data-pm25-limit="<?php echo PM25_LIMIT ?>">
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <nav class="navbar navbar-expand-md navbar-light bg-light">
            <a href="<?php echo l($device, 'sensors'); ?>" class="navbar-left"><img src="/public/img/dragon.png"/></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Nawigacja">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
                <?php foreach(array('sensors' => 'Strona główna', 'graphs' => 'Wykresy', 'about' => 'O stronie') as $action => $name): ?>
                <li class="nav-item">
                  <a class="nav-link <?php echo ($action == $current_action) ? 'active' : ''; ?>" href="<?php echo l($device, $action); ?>"><?php echo $name; ?></a>
                </li>
                <?php endforeach ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Wygląd
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <?php foreach(THEMES as $name => $desc): ?>
                    <a class="dropdown-item <?php echo ($name == $current_theme) ? 'active' : ''; ?>" href="<?php echo l($device, $current_action, array('theme' => $name)); ?>"><?php echo $desc ?></a>
                  <?php endforeach ?>
                  </div>
                </li>
                <?php if (count(CONFIG['devices']) > 1): ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Lokacje
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <?php foreach(CONFIG['devices'] as $d): ?>
                    <a class="dropdown-item <?php echo ($d == $device) ? 'active' : ''; ?>" href="<?php echo l($d, $current_action); ?>"><?php echo $d['description']; ?></a>
                  <?php endforeach ?>
                  </div>
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