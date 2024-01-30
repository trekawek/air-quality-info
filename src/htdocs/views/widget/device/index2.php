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
    <?php echo cssLink("public/css/style.css"); ?>
    <?php echo cssLink("public/css/font_aqi.css"); ?>
</head>

<body>
    <div class="container">
        <div class="row device-header">
            <div class="col-md offset-md-2">
                <?php include('partials/device_description.php'); ?>
            </div>
            <div class="col-md-auto text-center">
                <?php include('partials/sensors/badge.php') ?>
            </div>
            <div class="col-md offset-md-2">
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <?php include('partials/sensors/table.php') ?>
            </div>
        </div>
    </div>
    <footer class="text-muted text-center">
        <small>
            <?php if (isset($domainTemplate['widget_footer'])): ?>
            <?php echo $domainTemplate['widget_footer'] ?><br/>
            <?php endif ?>
            <?php echo __('Powered by ') ?><a href="https://aqi.eco" target="_blank">aqi.eco</a>.
        </small>
    </footer>
    <?php if (isset($domainTemplate['css_widget'])): ?>
    <?php echo "<style>\n".$domainTemplate['css_widget']."\n</style>\n" ?>
    <?php endif ?>
    <?php echo jsLink("admin/public/js/vendor.min.js"); ?>
</body>
</html>
