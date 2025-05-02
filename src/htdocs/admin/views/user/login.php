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
        <title>aqi.eco - <?php echo __('login'); ?></title>
        <?php echo cssLink("admin/public/css/vendor.min.css"); ?>
        <?php echo cssLink("admin/public/css/style.css"); ?>
    </head>

    <body class="app flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card-group">
                        <div class="card p-4">
                            <div class="card-body">
                                <?php $alert = $this->getAlert(); if ($alert): ?>
                                <div class="alert alert-<?php echo $alert['type'] ?>" role="alert">
                                    <?php echo $alert['message'] ?>
                                </div>
                                <?php endif ?>
                                <form class="form-signin" action="<?php echo l('user', 'login') ?>" method="POST">
                                    <h1><?php echo __('Login'); ?></h1>
                                    <p class="text-muted"><?php echo __('Sign in to your account'); ?></p>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="icon-user"></i>
                                            </span>
                                        </div>
                                        <input class="form-control" name="email" type="email" placeholder="<?php echo __('E-mail'); ?>" required>
                                    </div>
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="icon-lock"></i>
                                            </span>
                                        </div>
                                        <input class="form-control" name="password" type="password" placeholder="<?php echo __('Password'); ?>" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn btn-primary px-4" type="submit"><?php echo __('Login'); ?></button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <a href="<?php echo l('user', 'forgotPassword') ?>"><?php echo __('Forgot your password?')?></a>
                                        </div>
                                    </div>

                                    <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                                </form>
                            </div>
                        </div>
                        <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
                            <div class="card-body text-center">
                                <div>
                                    <h2><?php echo __('Sign up'); ?></h2>
                                    <p><?php echo __("Please create an account if you don't have one already."); ?></p>
                                    <a href="<?php echo l('user', 'register') ?>" class="btn btn-primary active mt-3"><?php echo __('Register now!'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo jsLink("admin/public/js/vendor.min.js"); ?>
    </body>
</html>