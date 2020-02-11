<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Domain widget list') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo __('Title') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($widgets as $i => $w): ?>
                        <tr>
                            <td><?php echo $i + 1 ?></td>
                            <td><?php echo htmlspecialchars($w['title']) ?></td>
                            <td>
                                <a href="<?php echo l('widget', 'edit', null, array('widget_id' => $w['id'])) ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                <a href="<?php echo l('widget', 'delete', null, array('widget_id' => $w['id'])) ?>" class="delete-link btn btn-danger"><i class="fa fa-trash-o "></i></a>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach ?>
                </table>
                <p><a href="<?php echo l('widget', 'create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a></p>
                <p><?php echo __('Widgets created here will show the average air quality calculated from all sensors.') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Device widget list') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo __('Device name') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($devices as $i => $d): ?>
                        <tr>
                            <td><?php echo $i + 1 ?></td>
                            <td><?php echo htmlspecialchars($d['name']) ?></td>
                            <td>
                                <a class="btn btn-success" href="<?php echo l('widget', 'showDeviceWidget', null, array('device_id' => $d['id'])) ?>"><i class="fa fa-search"></i></a>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach ?>
                </table>
                <p><?php echo __('Widgets created here will show the state of a single device.') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('Template widgets') ?>
            </div>
            <div class="card-body">
                <form action="<?php echo l('widget', 'updateWidgetSettings') ?>" method="post">
                    <?php $userForm->render() ?>
                    <button type="submit" class="btn btn-primary"><?php echo __('Update') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>