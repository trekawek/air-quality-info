<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <?php echo __('Device JSON list') ?>
            </div>
            <div class="card-body">
                <p><a href="<?php echo l('device', 'edit', null, array('device_id' => $deviceId)) ?>" class="btn btn-primary"><?php echo __('Back to the device') ?></a></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 10%" scope="col"><?php echo __('Time') ?></th>
                            <th style="width: 90%" scope="col"><?php echo __('Data') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($jsonUpdates as $t => $data): ?>
                        <tr>
                            <td><a href="<?php echo l('device_json', 'get', null, array('device_id' => $deviceId, 'timestamp' => $t)) ?>"><?php echo date("Y-m-d H:i:s", $t); ?></a></td>
                            <td><?php echo $data ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>