<?php
function navItem($action, $desc) {
  global $currentAction, $currentController;
  require('partials/navbar/nav_item.php');
}?><!DOCTYPE html>
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>aqi.eco</title>
        <link rel="stylesheet" href="/public/css/vendor.min.css"/>
        <link rel="stylesheet" href="/public/css/themes/default.min.css"/>
        <link rel="stylesheet" href="/admin/public/css/landing.css"/>
    </head>

<body>
    <div class="container">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">
            <img src="/public/img/aqi.png"/><span class="logo">aqi.eco</span>
        </a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php navItem(array('main', 'index'), 'Home'); ?>
                <?php navItem(array('user', 'login'), 'Sign in'); ?>
                <?php navItem(array('user', 'register'), 'Sign up'); ?>
                <?php navItem(array('main', 'support'), 'Support'); ?>
            </ul>
        </div>
    </nav>

    <div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto">
