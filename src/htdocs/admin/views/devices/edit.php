<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Edit device') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('device', 'edit', null, array('device_id' => $deviceId)) ?>" method="post">
                    <?php $deviceForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Update') ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Custom field mappings') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo __('JSON field') ?></th>
                            <th><?php echo __('Database field') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($mapping as $m): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($m['json_name']) ?></td>
                            <td><?php echo htmlspecialchars($m['db_name']) ?></td>
                            <td>
                                <a href="<?php echo l('device', 'deleteMapping', null, array('device_id' => $deviceId, 'mapping_id' => $m['id'])) ?>" class="btn btn-danger delete-link"><i class="fa fa-trash-o "></i></a>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach ?>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <?php echo __('Add custom field mapping') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('device', 'createMapping', null, array('device_id' => $deviceId)) ?>" method="post">
                    <?php $mappingForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Add mapping') ?></button>
                </form>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Last received data') ?>
            </div>
            <div class="card-body">
                
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <?php echo __('Received JSONs') ?>
            </div>
            <div class="card-body">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <?php echo __('Operations') ?>
            </div>
            <div class="card-body">
                <p><a href="#" class="btn btn-primary">Import data from Madavi.de</a></p>
                <p><a href="#" class="btn btn-danger">Delete device</a></p>
            </div>
        </div>
    </div>

</div>