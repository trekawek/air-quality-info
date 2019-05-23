<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Account info') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('user', 'edit') ?>" method="post">
                    <?php $userForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Update') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>