<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <?php echo __('Import data from Madavi.de') ?>
            </div>
            <div class="card-body">
                <p>
                    <a href="<?php echo $post ?>" class="btn btn-primary post-with-output" data-output="#import-logs" data-on-success="#import-finished" data-csrf-token="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"><?php echo __('Start') ?></a>
                    <a href="<?php echo l('device', 'edit', null, array('device_id'=>$deviceId)) ?>" class="btn btn-danger"><?php echo __('Back to the device') ?></a>
                </p>
                <p><span class="d-none badge badge-success" id="import-finished"><?php echo __('Import finished') ?></span></p>
                <pre class="p-1 pre-scrollable border" id="import-logs"></pre>
            </div>
        </div>
    </div>
</div>