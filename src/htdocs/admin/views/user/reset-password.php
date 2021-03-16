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
                            <?php $alert = $this->getAlert(); if ($alert): ?>
                                <div class="alert alert-<?php echo $alert['type'] ?>" role="alert">
                                    <?php echo $alert['message'] ?>
                                </div>
                            <?php endif ?>
                            <form class="form-signin" action="<?php echo l('user', 'resetPassword', null, array('token'=>$token)) ?>" method="POST">
                                <h1><?php echo __('Change your password') ?></h1>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="icon-lock"></i>
                                        </span>
                                    </div>
                                    <input class="form-control" name="password" type="password" placeholder="<?php echo __('Password'); ?>">
                                </div>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="icon-lock"></i>
                                        </span>
                                    </div>
                                    <input class="form-control" name="password2" type="password" placeholder="<?php echo __('Repeat password'); ?>">
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                                <button class="btn btn-block btn-success" type="submit"><?php echo __('Update password'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo jsLink("admin/public/js/vendor.min.js"); ?>
    </body>
</html>
