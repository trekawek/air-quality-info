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
        <title>aqi.eco - <?php echo __('register'); ?></title>
        <link rel="stylesheet" href="/admin/public/css/vendor.min.css"/>
        <link rel="stylesheet" href="/admin/public/css/style.css"/>
    </head>

    <body class="login-body">
        <div class="container">
            <form class="form-signin" action="<?php echo l('user', 'register') ?>" method="POST">
                <h2 class="form-signin-heading"><?php echo __('register'); ?></h2>
                <div class="login-wrap">
                    <?php if (isset($message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $message ?>
                    </div>
                    <?php endif ?>

                    <p><?php echo __('Please enter your account details below'); ?></p>
                    <input type="email" name="email" class="form-control" placeholder="<?php echo __('E-mail'); ?>" value="<?php echo $email ?>" autofocus>
                    <input type="password" name="password" class="form-control" placeholder="<?php echo __('Password'); ?>" value="<?php echo $password ?>">
                    <input type="password" name="password2" class="form-control" placeholder="<?php echo __('Re-type password'); ?>" value="<?php echo $password2 ?>">
                    
                    <div class="input-group">
                        <input type="text" name="domain" class="form-control" placeholder="Domain name" value="<?php echo $domain ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><?php echo CONFIG['user_domain_suffixes'][0] ?></span>
                        </div>
                    </div>

                    <button class="btn btn-lg btn-login btn-block" type="submit"><?php echo __('Submit'); ?></button>
                </div>
            </form>
        </div>
        <script src="/admin/public/js/vendor.min.js"></script>
    </body>
</html>
