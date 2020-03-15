
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require("partials/ga.php") ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>aqi.eco</title>
    <?php echo cssLink("public/css/vendor.min.css"); ?>
    <?php echo cssLink("public/css/themes/default.min.css"); ?>
    <?php echo cssLink("public/css/v-domain-widget.css"); ?>
    <?php echo cssLink("public/css/font_aqi.css"); ?>
</head>

<body>
    <div class="container">
      <a href="<?php echo $siteUrl ?>" target="_parent">
        <ul class="row list-group">
            <li class="list-group-item text-center air-quality-<?php echo $level === null ? 'null' : $level; ?>">
                <h5 class="mb-0 dropshadow white">
                    <?php echo $title ?>
                </h5>
                <small class="dropshadow white"><?php echo __('Current air quality') ?></small>
            </li>

            <?php if ($level === null): ?>
              <li class="list-group-item text-center air-quality-null">
                <h4 class="dropshadow white"><?php echo __('There are no data') ?></h4>
              </li>
            <?php else: ?>
              <li class="list-group-item text-center air-quality-<?php echo $level ?>">
                <i class="fa <?php echo $locale['icon'] ?> fa-5x dropshadow white"></i>
                <h4 class="dropshadow white"><?php echo $locale['label'] ?></h4>
              </li>

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
            <?php endif ?>
            <?php if (!$hideDetailsLink): ?>
            <li class="list-group-item text-center air-quality-<?php echo $level === null ? 'null' : $level; ?>">
              <small class="dropshadow white"><?php echo __('Click to see more details.') ?></small>
            </li>
            <?php endif ?>
        </ul>
      </a>

    </div>
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
