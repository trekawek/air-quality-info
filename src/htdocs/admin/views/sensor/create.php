<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <?php echo __('Link sensor.community device') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('sensor', 'create') ?>" method="post" class="sensor-community-map-group">
                    <div class="form-group">
                        <div class="map"></div>
                    </div>
                    <?php $deviceForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Link') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>