<ol class="breadcrumb">
<?php
function formatDate(\DateTimeInterface $dateTime) {
    $tz = new DateTimeZone(date_default_timezone_get());
    $dateTime->setTimezone($tz);
    return $dateTime->format("Y-m-d H:i:s");
}

$breadcrumbs = explode('/', $path);
$currentPath = array();
foreach($breadcrumbs as $segment):
$currentPath[] = $segment;
?>
    <?php $lastItem = (end($breadcrumbs) === $segment) ?>
    <li class="breadcrumb-item <?php echo $lastItem ? 'active' : '' ?>">
        <?php if (!$lastItem): ?>
        <a href="<?php echo l('csv', 'index', null, array('path' => implode('/', $currentPath))) ?>">
        <?php endif ?>
            <?php echo $segment ?>
        <?php if (!$lastItem): ?>
        </a>
        <?php endif ?>
    </li>
<?php endforeach ?>
</ol>


<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <?php echo __('CSV') ?>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo __('Name') ?></th>
                            <th><?php echo __('Last modification') ?></th>
                            <th><?php echo __('Size') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($list['dirs'] as $dir): ?>
                        <tr>
                            <td>
                                <a href="<?php echo l('csv', 'index', null, array('path' => $dir)) ?>" ?>
                                <i class="fa fa-folder"></i>
                                <?php echo htmlspecialchars(basename($dir)) ?>
                                </a>
                            </td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                                <a href="<?php echo l('csv', 'downloadDir', null, array('path' => $dir)) ?>" class="btn btn-primary"><i class="fa fa-download"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>

                    <?php foreach($list['objects'] as $object): ?>
                        <tr>
                            <td>
                                <a href="<?php echo l('csv', 'downloadFile', null, array('path' => $object['Key'])) ?>" ?>
                                    <i class="fa fa-file"></i>
                                    <?php echo htmlspecialchars(basename($object['Key'])) ?>
                                </a>
                            </td>
                            <td><?php echo formatDate($object['LastModified']) ?></td>
                            <td><?php echo $object['Size'] ?></td>
                            <td>-</td>
                        </tr>
                    <?php endforeach ?>

                    </tbody>
                </table>
            </div>            
        </div>
    </div>
</div>