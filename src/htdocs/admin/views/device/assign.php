<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Assign device') ?>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-lg-6"><?php echo __('Name') ?></dt>
                    <dd class="col-lg-6"><?php echo $device['name']; ?></dd>
                </dl>

                <form action="<?php echo l('device', 'assign', null, array('key' => $assignToken)) ?>" method="post">
                    <?php $deviceForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Assign') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>