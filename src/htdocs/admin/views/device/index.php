<div class="alert alert-danger" role="alert">
  <?php echo __('If you device has stopped sending data, please check <a href="https://aqi.eco/news">the news page</a>.'); ?>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <?php echo __('Device list') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo __('Device name') ?></th>
                            <th><?php echo __('Description') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($devices as $i => $d): ?>
                        <tr>
                            <td><?php echo $i + 1 ?></td>
                            <td><?php echo htmlspecialchars($d['name']) ?></td>
                            <td><?php echo htmlspecialchars($d['description']) ?></td>
                            <td>
                                <form action="<?php echo l('device', 'move', null, array('device_id' => $d['id'])) ?>" method="post">
                                    <?php if ($d['path'] !== null): ?>
                                    <a href="<?php echo $uriPrefix . $d['path'] ?>" class="btn btn-warning"><i class="fa fa-globe"></i></a>
                                    <?php endif ?>
                                    <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                                    <a href="<?php echo l('device', 'edit', null, array('device_id' => $d['id'])) ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                    <a href="<?php echo l('device', 'deleteDevice', null, array('device_id' => $d['id'])) ?>" class="delete-link btn btn-danger"><i class="fa fa-trash-o "></i></a>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach ?>
                </table>
                <a href="<?php echo l('device', 'create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __('Add your own device') ?></a>
                <?php if ($this->user['allow_sensor_community']): ?>
                <a href="<?php echo l('sensor', 'create') ?>" class="btn btn-warning"><i class="fa fa-plus"></i> <?php echo __('Link sensor.community device') ?></a>
                <a href="<?php echo l('sensor', 'createCustom') ?>" class="btn btn-warning"><i class="fa fa-plus"></i> <?php echo __('Link other device') ?></a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>