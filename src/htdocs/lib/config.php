<?php
function createDeviceTree() {
    $tree = array('children' => array());
    $i = 0;
    $nodeById = array();
    foreach (CONFIG['devices'] as $d) {
        $name = array_map('trim', explode('/', $d['name']));
        $desc = array_map('trim', explode('/', $d['description']));
        $node = &$tree;
        foreach ($desc as $s) {
            if (!isset($node['children'][$s])) {
                $node['children'][$s] = array('children' => array());
                $node['id'] = $i;
                $nodeById[$i] = &$node;
                $i++;
            }
            $node = &$node['children'][$s];
        }
        $node['name'] = $d['name'];
    }
    return array('tree' => $tree, 'nodeById' => $nodeById);
}
?>