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
                            <th><?php echo __('ESP8266 ID') ?></th>
                            <th><?php echo __('Description') ?></th>
                            <th><?php echo __('Visibility') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($devices as $i => $d): ?>
                        <tr>
                            <td><?php echo $i + 1 ?></td>
                            <td><?php echo htmlspecialchars($d['name']) ?></td>
                            <td><?php echo htmlspecialchars($d['esp8266_id']) ?></td>
                            <td><?php echo htmlspecialchars($d['description']) ?></td>
                            <td>
                            <?php if ($d['hidden']): ?>
                                <span class="badge badge-danger"><?php echo __('Hidden') ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><?php echo __('Visible') ?></span>
                            <?php endif ?>
                            </td>
                            <td>
                                <a href="<?php echo l('device', 'edit', null, array('device_id' => $d['id'])) ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                <a href="<?php echo l('device', 'deleteDevice', null, array('device_id' => $d['id'])) ?>" class="delete-link btn btn-danger"><i class="fa fa-trash-o "></i></a>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach ?>
                </table>

                <a href="<?php echo l('device', 'create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
</div>