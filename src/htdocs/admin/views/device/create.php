<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Add new device') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('device', 'create') ?>" method="post">
                    <?php $deviceForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Create') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>