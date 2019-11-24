<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Add new widget') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('widget', 'create') ?>" method="post">
                    <?php $widgetForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Create') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>