<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require("partials/ga.php") ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>aqi.eco<?php echo $title ? (' - ' . $title) : '' ?></title>
        <link rel="stylesheet" href="/admin/public/css/vendor.min.css?v=31"/>
        <link rel="stylesheet" href="/admin/public/css/style.css?v=33"/>
        <link rel="shortcut icon" type="image/png" href="/public/img/aqi-favicon.png"/>
        <link rel="apple-touch-icon" sizes="512x512" href="/public/img/aqi-512.png">
    </head>

    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show" data-locale='<?php echo json_encode($currentLocale->getJsMessages()) ?>'>
        <header class="app-header navbar">
            <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
                <span class="navbar-toggler-icon"></span>
            </button>
        <a class="navbar-brand" href="/">
            <img src="/public/img/aqi.png"/><span class="logo">aqi.eco</span>
        </a>
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
                            <a class="nav-link" href="<?php echo l('device_hierarchy', 'index') ?>">
                            <i class="nav-icon fa fa-sitemap"></i> <?php echo __('Locations') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo l('csv', 'index') ?>">
                            <i class="nav-icon fa fa-archive"></i> <?php echo __('CSV archive') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo l('user', 'settings') ?>">
                            <i class="nav-icon fa fa-cog"></i> <?php echo __('Settings') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo l('template', 'edit') ?>">
                            <i class="nav-icon fa fa-code"></i> <?php echo __('Templates') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo l('widget', 'index') ?>">
                            <i class="nav-icon fa fa-window-maximize"></i> <?php echo __('Widgets') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo l('user', 'edit') ?>">
                            <i class="nav-icon fa fa-user"></i> <?php echo __('Account') ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="<?php echo l('main', 'static', null, array('pageName' => 'support')) ?>">
                            <i class="nav-icon fa fa-dollar"></i> <?php echo __('Support') ?></a>
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

                    <?php $alert = $this->getAlert(); if ($alert): ?>
                    <div class="alert alert-<?php echo $alert['type'] ?>" role="alert">
                        <?php echo $alert['message'] ?>
                    </div>
                    <?php endif ?>

                    <p></p>