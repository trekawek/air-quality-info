<!DOCTYPE html>
<html lang="<?php echo $currentLocale->getCurrentLang(); ?>">
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
        <title>aqi.eco - <?php echo __('register'); ?></title>
        <?php echo cssLink("admin/public/css/vendor.min.css"); ?>
        <?php echo cssLink("admin/public/css/style.css"); ?>
    </head>

    <body class="app flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mx-4">
                        <div class="card-body p-4">
                            <?php if (isset($message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $message ?>
                            </div>
                            <?php endif ?>
                            <form class="form-signin" action="<?php echo l('user', 'register') ?>" method="POST">
                                <h1><?php echo __('Register') ?></h1>
                                <p class="text-muted"><?php echo __('Create your account') ?></p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">@</span>
                                    </div>
                                    <input class="form-control" name="email" type="email" placeholder="<?php echo __('E-mail'); ?>" value="<?php echo $email ?>">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="icon-lock"></i>
                                        </span>
                                    </div>
                                    <input class="form-control" name="password" type="password" placeholder="<?php echo __('Password'); ?>"  value="<?php echo $password ?>">
                                </div>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="icon-lock"></i>
                                        </span>
                                    </div>
                                    <input class="form-control" name="password2" type="password" placeholder="<?php echo __('Repeat password'); ?>"  value="<?php echo $password2 ?>">
                                </div>
                                <div class="input-group mb-4">
                                    <input class="form-control" name="domain" type="text" placeholder="<?php echo __('Domain name'); ?>" value="<?php echo $domain ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><?php echo CONFIG['user_domain_suffixes'][0] ?></span>
                                    </div>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                                <button class="btn btn-block btn-success" type="submit">Create Account</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo jsLink("admin/public/js/vendor.min.js"); ?>
    </body>
</html>