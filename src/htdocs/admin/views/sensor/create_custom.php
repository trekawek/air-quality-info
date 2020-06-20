<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Link other device') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('sensor', 'create_custom') ?>" method="post">
                    <?php $deviceForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Link') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>