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
        <title>aqi.eco - <?php echo __('login'); ?></title>
        <link rel="stylesheet" href="/admin/public/css/vendor.min.css"/>
        <link rel="stylesheet" href="/admin/public/css/style.css"/>
    </head>

    <body class="login-body">
        <div class="container">
            <form class="form-signin" action="<?php echo l('user', 'login') ?>" method="POST">
                <h2 class="form-signin-heading"><?php echo __('sign in'); ?></h2>
                <div class="login-wrap">
                    <?php if (isset($message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $message ?>
                    </div>
                    <?php endif ?>

                    <input type="email" name="email" class="form-control" placeholder="<?php echo __('E-mail'); ?>" autofocus>
                    <input type="password" name="password" class="form-control" placeholder="<?php echo __('Password'); ?>">
                    
                    <button class="btn btn-lg btn-login btn-block" type="submit"><?php echo __('Sign in'); ?></button>
                    <div class="registration">
                        <?php echo __("Doesn't have an account yet?"); ?>
                        <a class="" href="<?php echo l('user', 'register') ?>">
                            <?php echo __("Create an account"); ?>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <script src="/admin/public/js/vendor.min.js"></script>
    </body>
</html>
