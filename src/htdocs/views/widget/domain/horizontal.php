
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
  <?php if ($level === null): ?>
    <div class="row-1 air-quality-null">
    <div class="column-1">
      <ul class="row list-group">
          <li style="padding:10px;">
              <h5 class="mb-0 dropshadow white"><?php echo $title ?></h5>
              <small class="dropshadow white"><?php echo __('Current air quality') ?></small>
          </li>

          <li class="text-center">
              <i class="fa fa-frown-o fa-3x dropshadow white"></i>
              <h4 class="dropshadow white"><?php echo __('There are no data') ?></h4>
          </li>
        </ul>
    </div>
    </div>
  <?php else: ?>
    <div class="row-1 air-quality-<?php echo $level ?>">
      <div class="column-1">
        <ul class="row list-group">
            <li style="padding:10px;">
                <h5 class="mb-0 dropshadow white"><?php echo $title ?></h5>
                <small class="dropshadow white"><?php echo __('Current air quality') ?></small>
            </li>

            <li class="text-center">
                <i class="fa <?php echo $locale['icon'] ?> fa-3x dropshadow white"></i>
                <h4 class="dropshadow white"><?php echo $locale['label'] ?></h4>
            </li>
          </ul>
      </div>

      <div class="column-2">
        <ul class="row">
          <?php foreach ($locale['recommendations'] as $r): ?>
          <li class="list-group-item text-center air-quality-<?php echo $level ?>">
            <span class="strip"><i class="<?php echo $r['icon'] ?> <?php echo $r['color'] ?>"></i>
              <span class="strip-recommended">
                <strong><?php echo $r['label'] ?></strong>
                <br />
                <span class="strip-recommended-description"><?php echo $r['description'] ?></span>
              </span>
            </span>
          </li>
          <?php endforeach ?>
        </ul>
      </div>
    </div>
  <?php endif ?>
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
