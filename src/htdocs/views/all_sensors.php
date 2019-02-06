<?php
function parseTree($node) {
    $list = array();
    if (isset($node['name'])) {
        $list[] = $node['name'];
    }
    if (isset($node['children'])) {
        foreach ($node['children'] as $n) {
            array_push($list, ...parseTree($n));
        }
    }
    return $list;
}

$deviceGroupId = 0;
if (isset($uri[0])) {
    $deviceGroupId = $uri[0];
}

$nodeById = createDeviceTree()['nodeById'];
if (!isset($nodeById[$deviceGroupId])) {
    $deviceGroupId = 0;
}
$node = $nodeById[$deviceGroupId];
$devices = parseTree($node);
?>
<?php include('partials/head.php'); ?>

<?php include('partials/sensors/avg-switch.php') ?>
<?php
foreach ($devices as $device_name) {
    foreach (CONFIG['devices'] as $device) {
        if ($device['name'] == $device_name) {
            break;
        }
    }

    $sensors = create_dao($device)->getLastData();
    $current_avg_type = '1';
    if (isset($_GET['avg_type']) && $_GET['avg_type'] == '24') {
        $current_avg_type = '24';
    }

    $averages = getAverages($dao, $sensors, $current_avg_type);
    $desc = array_map('trim', explode('/', $device['description']));
?>

<div class="row">
    <div class="col-md-3 offset-md-2">
        <small><?php echo implode(' / ', array_slice($desc, 0, -1)) ?></small>
        <h4>
            <a href="<?php echo l($device, 'index') ?>"><?php echo end($desc) ?></a>
        </h4>
    </div>
    <div class="col-md-2 text-center">
        <?php include('partials/sensors/badge.php') ?>
    </div>
</div>

<?php
    include('partials/sensors/table.php');
?>
<?php
}
?>
<?php include('partials/tail.php'); ?>