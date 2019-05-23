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
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>aqi.eco<?php echo $title ? (' - ' . $title) : '' ?></title>
        <link rel="stylesheet" href="/admin/public/css/vendor.min.css"/>
        <link rel="stylesheet" href="/admin/public/css/style.css"/>
    </head>

    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
        <header class="app-header navbar">
            <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-brand">
                <img src="/public/img/dragon.png">
                Air Quality Info
                <sup>&beta;</sup>
            </div>
        </header>

        <div class="app-body">
            <div class="sidebar">
                <nav class="sidebar-nav">
                    <ul class="nav">
                        <li class="nav-title">
                            <?php echo __('Dashboard') ?>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo l('device', 'index') ?>">
                            <i class="nav-icon fa fa-tachometer"></i> <?php echo __('Devices') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo l('user', 'logout') ?>">
                            <i class="nav-icon fa fa-sign-out"></i> <?php echo __('Logout') ?></a>
                        </li>
                    </ul>
                </nav>
            </div>

            <main class="main">
                <div class="container-fluid">
                    <div id="ui-view">
                    <p></p>