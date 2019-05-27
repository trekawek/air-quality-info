<?php include('breadcrumbs.php') ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <?php echo __('Link device') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('device_hierarchy', 'createDevice', null, array('node_id' => $parentId)) ?>" method="post">
                    <?php $nodeForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Link device') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>