<?php include(__DIR__ . '/../device_hierarchy/breadcrumbs.php') ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Edit sensor') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('sensor', 'edit', null, array('device_id' => $deviceId)) ?>" method="post">
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
                <?php echo __('Sensors id list') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo __('Sensor id') ?></th>
                            <th><?php echo __('Sensor type') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($sensorIds as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['sensor_id']) ?></td>
                            <td><?php echo htmlspecialchars($r['type']) ?></td>
                            <td>
                                <a href="<?php echo l('sensor', 'deleteSensorId', null, array('device_id' => $deviceId, 'sensor_id' => $r['sensor_id'])) ?>" class="btn btn-danger delete-link"><i class="fa fa-trash-o "></i></a>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach ?>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <?php echo __('Add sensor id') ?>
            </div>
            <div class="card-body">
                <p><?php echo __('Find sensor on') ?> <a href="https://maps.sensor.community/#16/<?php echo $device['lat'] ?>/<?php echo $device['lng'] ?>">maps.sensor.community</a>.</p>
                <form action="<?php echo l('sensor', 'edit', null, array('device_id' => $deviceId)) ?>" method="post">
                    <?php $sensorIdForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Add sensor id') ?></button>
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
    </div>
</div>