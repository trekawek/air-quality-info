<?php
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>aqi.eco</title>
        <?php echo cssLink("public/css/vendor.min.css"); ?>
        <?php echo cssLink("public/css/themes/default.min.css"); ?>
        <?php echo cssLink("public/css/flags.css"); ?>
        <?php echo cssLink("admin/public/css/landing.css"); ?>
        <link rel="shortcut icon" type="image/png" href="/public/img/aqi-favicon.png"/>
        <link rel="apple-touch-icon" sizes="512x512" href="/public/img/aqi-512.png">
    </head>

<body data-locale='<?php echo json_encode($currentLocale->getJsMessages()) ?>'>
    <div class="container">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">
            <img src="/public/img/aqi.png"/><span class="logo">aqi.eco</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php navItem(array('main', 'index'), 'Home'); ?>
                <?php navItem(array('main', 'news'), 'News'); ?>
                <?php /* navItem(array('map', 'index'), 'Map'); */ ?>
                <?php if ($this->user): ?>
                <?php navItem(array('device', 'index'), 'Dashboard'); ?>
                <?php else: ?>
                <?php navItem(array('user', 'login'), 'Sign in'); ?>
                <?php navItem(array('user', 'register'), 'Sign up'); ?>
                <?php endif ?>
                <?php navItem('/about/contact', 'Contact'); ?>
                <?php navItem('/about/support', 'Support'); ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-globe" aria-hidden="true"></i>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
<?php include('partials/flags.php'); ?>
                  </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="pt-3 mx-auto">
