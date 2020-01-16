<?php include(__DIR__ . '/../device_hierarchy/breadcrumbs.php') ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Edit device') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('device', 'edit', null, array('device_id' => $deviceId)) ?>" method="post">
                    <?php $deviceForm->render() ?>
                    <div class="form-group collapse map-control map-group <?php echo $device['location_provided'] ? 'show' : '' ?>" data-input-lat="#latInput" data-input-lng="#lngInput" data-input-elevation="#elevationInput">
                        <div class="map"></div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo __('Update') ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Sensor configuration') ?>
            </div>
            <div class="card-body">
                <p><?php echo __('Please use following data to configure your sensor') ?>:</p>
                <dl class="row">
                    <dt class="col-lg-6"><?php echo __('Send data to own API') ?></dt>
                    <dd class="col-lg-6"><input type="checkbox" class="form-control" checked disabled/></dd>

                    <dt class="col-lg-6"><?php echo __('Server') ?></dt>
                    <dd class="col-lg-6"><input type="text" class="form-control" value="<?php echo $this->user['domain'] . CONFIG['user_domain_suffixes'][0] ?>" disabled/></dd>

                    <dt class="col-lg-6"><?php echo __('Path') ?></dt>
                    <dd class="col-lg-6"><input type="text" class="form-control" value="/u/<?php echo $device['api_key']; ?>" disabled/></dd>

                    <dt class="col-lg-6"><?php echo __('Port') ?></dt>
                    <dd class="col-lg-6"><input type="text" class="form-control" value="443" disabled/></dd>
                </dl>
            </div>
        </div>

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
                    <a class="btn btn-primary" href="<?php echo l('device', 'importMadaviWrapper', null, array('device_id' => $deviceId)) ?>">
                        <?php echo __('Import data from Madavi.de') ?>
                    </a>
                </p>

                <p>
                    <form action="<?php echo l('device', 'resetHttpPassword', null, array('device_id' => $deviceId)) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                        <button type="submit" class="btn btn-danger"><?php echo __('Reset HTTP password') ?></button>
                    </form>
                </p>

                <p>
                    <?php if ($device['default_device']): ?>
                    <?php echo __('This is default device') ?>
                    <?php else: ?>
                    <form action="<?php echo l('device', 'makeDefault', null, array('device_id' => $deviceId)) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                        <button type="submit" class="btn btn-success"><?php echo __('Make device default') ?></button>
                    </form>
                    <?php endif ?>
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