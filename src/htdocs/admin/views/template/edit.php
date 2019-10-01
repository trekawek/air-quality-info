<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <?php echo __('Templates') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('template', 'edit') ?>" method="post" enctype="multipart/form-data">
                    <?php $templateForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Update') ?></button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <?php echo __('Guide') ?>
            </div>
            <div class="card-body">
                <img style="width: 100%" src="/admin/public/img/template-guide.png" />
            </div>
        </div>
    </div>
</div>