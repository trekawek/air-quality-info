<?php include('breadcrumbs.php') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <?php echo __('Device hierarchy') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo __('Open') ?></th>
                            <th><?php echo __('Name') ?></th>
                            <th><?php echo __('Description') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($nodes as $i => $d): ?>
                        <tr>
                            <td>
                            <?php if ($d['device_id'] === null): ?>
                            <a href="<?php echo l('device_hierarchy', 'index', null, array('node_id' => $d['id'])) ?>" class="btn btn-warning" ?><i class="fa fa-folder-o"></i></a>
                            <?php else: ?>
                            <a href="<?php echo l('device', 'edit', null, array('device_id' => $d['device_id'])) ?>" class="btn btn-primary" ?><i class="fa fa-dashboard"></i></a>
                            <?php endif ?>
                            </td>
                            <td><?php echo htmlspecialchars($d['name']) ?></td>
                            <td><?php echo htmlspecialchars($d['description']) ?></td>
                            <td>
                                <form action="<?php echo l('device_hierarchy', 'move', null, array('node_id' => $d['id'])) ?>" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>
                                    <button type="submit" name="move" value="up" class="btn btn-success" <?php echo $d['position'] == 0 ? 'disabled' : '' ?>><i class="fa fa-arrow-up"></i></button>
                                    <button type="submit" name="move" value="down" class="btn btn-success" <?php echo $d['position'] == count($nodes) - 1 ? 'disabled' : '' ?>><i class="fa fa-arrow-down"></i></button>
                                    <?php if ($d['device_id'] === null): ?>
                                    <a href="<?php echo l('device_hierarchy', 'editDirectory', null, array('node_id' => $d['id'])) ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                    <?php else: ?>
                                    <a href="<?php echo l('device_hierarchy', 'editDevice', null, array('node_id' => $d['id'])) ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                    <?php endif ?>
                                    <a href="<?php echo l('device_hierarchy', 'deleteNode', null, array('node_id' => $d['id'])) ?>" class="delete-link btn btn-danger"><i class="fa fa-trash-o "></i></a>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach ?>
                </table>
                <a href="<?php echo l('device_hierarchy', 'createDir', null, array('node_id' => $nodeId)) ?>" class="btn btn-warning"><i class="fa fa-plus"></i> <?php echo __('Create directory') ?></a>
                <a href="<?php echo l('device_hierarchy', 'createDevice', null, array('node_id' => $nodeId)) ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __('Create device') ?></a>
            </div>            
        </div>
    </div>
</div>