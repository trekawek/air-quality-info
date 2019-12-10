<div class="row">
    <div class="col-lg-<?php echo $widgetTemplate == 'vertical' ? 6 : 12 ?>">
        <div class="card">
            <div class="card-header">
                <?php echo __('Widget') ?>
            </div>
            <div class="card-body text-center">
                <?php include('widget.php'); ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <?php echo __('Edit widget') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('widget', 'edit', null, array('widget_id' => $widgetId)) ?>" method="post">
                    <?php $widgetForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Update') ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <?php echo __('Widget source') ?>
            </div>
            <div class="card-body">
                <textarea readonly col="80" rows="10" class="form-control" style="font-family: monospace;"><?php include('widget.php'); ?></textarea>
            </div>
        </div>
    </div>
</div>