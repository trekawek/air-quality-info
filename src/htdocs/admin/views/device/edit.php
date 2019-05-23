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
                <form action="<?php echo l('device', 'edit', null, array('device_id' => $deviceId)) ?>" method="post">
                    <?php $mappingForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Add mapping') ?></button>
                </form>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Operations') ?>
            </div>
            <div class="card-body">
                <p>
                    <form action="<?php echo l('device', 'importMadavi', null, array('device_id' => $deviceId)) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                        <button type="submit" class="btn btn-primary"><?php echo __('Import data from Madavi.de') ?></button>
                    </form>
                </p>

                <p>
                    <form action="<?php echo l('device', 'resetHttpPassword', null, array('device_id' => $deviceId)) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                        <button type="submit" class="btn btn-danger"><?php echo __('Reset HTTP password') ?></button>
                    </form>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <?php echo __('Last received data') ?>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-lg-6"><?php echo __('Last update') ?></dt>
                    <dd class="col-lg-6"><?php echo date("Y-m-d H:i:s", $lastRecord['last_update']); ?></dd>

                    <?php foreach($lastRecord as $key => $value): ?>
                    <?php if ($key == 'last_update') continue; ?>
                    <dt class="col-lg-6"><?php echo $key ?></dt>
                    <dd class="col-lg-6"><?php echo $value ?></dd>
                    <?php endforeach ?>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <?php echo __('Received JSONs') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo __('Time') ?></th>
                            <th scope="col"><?php echo __('Data') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($jsonUpdates as $t => $data): ?>
                        <tr>
                            <td><?php echo date("Y-m-d H:i:s", $t); ?></td>
                            <td><?php echo substr($data, 0, 64) ?><a href="<?php echo l('device_json', 'get', null, array('device_id' => $deviceId, 'timestamp' => $t)) ?>">...</a></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
                <a href="<?php echo l('device_json', 'index', null, array('device_id' => $deviceId)) ?>" class="btn btn-primary"><?php echo __('Show all') ?></a>
            </div>
        </div>
    </div>
</div>