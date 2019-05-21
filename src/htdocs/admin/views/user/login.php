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
                    <label class="checkbox">
                        <input type="checkbox" name="remember" value="true"> <?php echo __('Remember me'); ?><br/>
                        <span class="pull-right"><a data-toggle="modal" href="#myModal"> <?php echo __('Forgot password?'); ?></a></span>
                    </label>
                    <button class="btn btn-lg btn-login btn-block" type="submit"><?php echo __('Sign in'); ?></button>
                    <div class="registration">
                        <?php echo __("Doesn't have an account yet?"); ?>
                        <a class="" href="<?php echo l('user', 'register') ?>">
                            <?php echo __("Create an account"); ?>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Modal -->
            <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?php echo __('Forgot password?'); ?></h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><?php echo __('Enter you e-mail below to reset your password'); ?></p>
                            <input type="text" name="email" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo __('Cancel'); ?></button>
                            <button class="btn btn-success" type="button"><?php echo __('Submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/admin/public/js/vendor.min.js"></script>
    </body>
</html>
