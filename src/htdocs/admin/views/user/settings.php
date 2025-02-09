<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <?php echo __('Settings') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('user', 'settings') ?>" method="post">
                    <?php $userForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Update') ?></button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <?php echo __('TTN token') ?>
            </div>
            <div class="card-body">
                <p><?php echo __('Please use following data to configure your TTN application') ?>:</p>
                <dl>
                    <dt><?php echo __('Webhook') ?></dt>
                    <dd class="text-monospace">https://<?php echo 'api' . CONFIG['user_domain_suffixes'][0] . '/update-ttn/' . $user['ttn_api_key']; ?></dd>
                </dl>
                <p><?php echo __('The TTN device ID should match the aqi.eco device name or <b>TTN Device ID</b> value set in the <a href="/device">Devices</a>.') ?></p>

                <form action="<?php echo l('user', 'settings') ?>" method="post">
                    <?php $ttnForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Generate new token') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>