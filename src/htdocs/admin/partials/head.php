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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="apple-touch-icon" sizes="512x512" href="/public/img/dragon-512.png">

        <title>aqi.eco<?php echo $title ? (' - ' . $title) : '' ?></title>
        <link rel="stylesheet" href="/admin/public/css/vendor.min.css"/>
        <link rel="stylesheet" href="/admin/public/css/style.css"/>
    </head>

    <body class="boxed-page">
        <div class="container">
            <section id="container">
                <header class="header white-bg">
                    <div class="container">
                        <!--logo start-->
                        <a href="index.html" class="logo">aqi.<span>eco</span></a>
                        <!--logo end-->
                        <div class="nav notify-row" id="top_menu">
                            <!--  notification start -->
                            <ul class="nav top-menu">
                                <?php if ($notifications): ?>
                                <!-- notification dropdown start-->
                                <li id="header_notification_bar" class="dropdown">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                        <i class="fa fa-bell-o"></i>
                                        <span class="badge badge-warning"><?php echo count($notifications) ?></span>
                                    </a>
                                    <ul class="dropdown-menu extended notification">
                                        <div class="notify-arrow notify-arrow-yellow"></div>
                                        <li>
                                            <p class="yellow"><?php printf(__("You have %d new notifications"), count($notifications)) ?></p>
                                        </li>
                                        <?php foreach($notifications as $n): ?>
                                        <li>
                                            <a href="#">
                                                <span class="label label-<?php echo $n['type'] ?>"><i class="fa fa-bolt"></i></span>
                                                <?php echo $n['text'] ?>
                                            </a>
                                        </li>
                                        <?php endforeach ?>
                                    </ul>
                                </li>
                                <?php endif ?>
                            </ul>
                        </div>
                    </div>
                </header>

                <aside>
                    <div id="sidebar">
                        <!-- sidebar menu start-->
                        <ul class="sidebar-menu">
                            <li>
                                <a href="<?php echo l('device', 'index') ?>">
                                    <i class="fa fa-dashboard"></i>
                                    <span><?php echo __('Devices') ?></span>
                                </a>
                            </li>


                            <li>
                                <a href="<?php echo l('user', 'logout') ?>">
                                    <i class="fa fa-sign-out"></i>
                                    <span><?php echo __('Logout') ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>

                <section id="main-content">
                    <section class="wrapper">
