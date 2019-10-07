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
    <style>
.index-cat-0 {
  background-color: #57b108;
  color: #ffffff;
}

.index-cat-1 {
  background-color: #b0dd10;
  color: #262626;
}

.index-cat-2 {
  background-color: #ffd911;
  color: #262626;
}

.index-cat-3 {
  background-color: #e58100;
  color: #262626;
}

.index-cat-4 {
  background-color: #990000;
  color: #ffffff;
}

    </style>
</head>

<body>
    <div class="container">
        <ul class="row list-group">
            <li class="list-group-item text-center index-cat-<?php echo $averages['max_level']; ?>">
                <small><?php echo __('Pollution level') ?>:</small>
                <h4 class="mb-0">
                    <?php if ($averages['max_level'] !== null): ?>
                    <?php echo __(\AirQualityInfo\Lib\PollutionLevel::POLLUTION_LEVELS[$averages['max_level']]['name']); ?>
                    <?php else: ?>
                    <?php echo __('There are no data') ?>
                    <?php endif ?>
                </h4>
            </li>

            <li class="list-group-item text-center pb-0 pt-0">
                <small>
                <?php if(isset($breadcrumbs) && count($breadcrumbs) > 2): ?>
                    <?php include('partials/sensors/breadcrumbs.php'); ?>
                <?php endif ?>

                <a href="<?php echo $deviceUrl ?>" target="_blank">
                    <i class="fa fa-map-marker"></i>
                    <?php echo $device['description'] ?>
                </a>

                <?php if (!empty($device['extra_description'])): ?>
                    <br/>
                    <?php echo $device['extra_description'] ?>
                <?php endif ?>
                </small>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="wi wi-dust"></i> PM<sub>2.5</sub></span>
                <span class="badge badge-warning badge-pill"><?php echo round($averages['values']['pm25'], 0); ?> µg/m<sup>3</sup></span>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="wi wi-dust"></i> PM<sub>10</sub></span>
                <span class="badge badge-warning badge-pill"><?php echo round($averages['values']['pm10'], 0); ?> µg/m<sup>3</sup></span>
            </li>

            <?php if ($sensors['temperature'] !== null): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="wi wi-thermometer"></i> <?php echo __('Temperature') ?></span>
                <span class="badge badge-info badge-pill"><?php echo round($sensors['temperature'], 1); ?> &deg;C</span>
            </li>
            <?php endif ?>

            <?php if ($sensors['pressure'] !== null): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="wi wi-barometer"></i> <?php echo __('Pressure') ?></span>
                <span class="badge badge-info badge-pill"><?php echo round($sensors['pressure'], 0); ?> hPa</span>
            </li>
            <?php endif ?>

            <?php if ($sensors['humidity'] !== null): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="wi wi-humidity"></i> <?php echo __('Humidity') ?></span>
                <span class="badge badge-info badge-pill"><?php echo round($sensors['humidity'], 0); ?>%</span>
            </li>
            <?php endif ?>
        </ul>

    </div>
    <footer class="text-muted text-center">
        <small>
            <?php if (isset($domainTemplate['widget_footer'])): ?>
            <?php echo $domainTemplate['widget_footer'] ?><br/>
            <?php endif ?>
            <?php echo __('Powered by ') ?><a href="https://aqi.eco">aqi.eco</a>.
        </small>
    </footer>
    <script src="/admin/public/js/vendor.min.js"></script>
</body>
</html>