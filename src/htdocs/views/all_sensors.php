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
<?php var_dump($devices); ?>
<?php include('partials/tail.php'); ?>