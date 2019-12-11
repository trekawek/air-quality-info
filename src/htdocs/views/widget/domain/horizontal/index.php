
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require("partials/ga.php") ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>aqi.eco</title>
    <link rel="stylesheet" href="/public/css/vendor.min.css"/>
    <link rel="stylesheet" href="/public/css/themes/default.min.css"/>
    <link rel="stylesheet" href="/public/css/h-domain-widget.css"/>
    <link rel="stylesheet" href="/public/css/font_aqi.css"/>
</head>

<body>
  <a href="<?php echo $siteUrl ?>" target="_parent" class="aqibox-h">
    <?php include(sprintf('%s/level-%s.php', 'pl'/*$currentLocale->getCurrentLang()*/, $level === null ? 'null' : $level)); ?>
  </a>

  <footer class="text-muted text-center">
      <small>
          <?php if (isset($domainTemplate['widget_footer'])): ?>
          <?php echo $domainTemplate['widget_footer'] ?><br/>
          <?php endif ?>
          <?php echo __('Powered by ') ?><a href="https://aqi.eco" target="_blank">aqi.eco</a>.
      </small>
  </footer>
  <script>
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod === "attachEvent" ? "onload" : "load";
eventer(messageEvent, function(e) {
  parent.postMessage({
    'aqi-widget': <?php echo $widgetId ?>,
    'frameHeight': document.body.scrollHeight
  }, '*');
});
  </script>
</body>
</html>
