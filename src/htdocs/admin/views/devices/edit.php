<form action="<?php echo l('device', 'edit', null, array('device_id' => $deviceId)) ?>" method="post">
    <?php $form->render() ?>
    <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
    <button type="submit" class="btn btn-primary"><?php echo __('Submit') ?></button>
</form>