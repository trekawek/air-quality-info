
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Widget') ?>
            </div>
            <div class="card-body text-center">
                <?php include('widget.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Widget source') ?>
            </div>
            <div class="card-body">
            <textarea readonly col="80" rows="4" class="form-control" style="font-family: monospace;"><?php include('widget.php'); ?></textarea>
            </div>
        </div>
    </div>
</div>