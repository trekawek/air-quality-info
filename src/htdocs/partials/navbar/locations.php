<?php if (count(CONFIG['devices']) > 1) {
require_once('partials/navbar/children.php');
$tree = array('children' => array());
foreach (CONFIG['devices'] as $d) {
    $desc = array_map('trim', explode('/', $d['description']));
    $node = &$tree;
    foreach ($desc as $s) {
        if (!isset($node['children'][$s])) {
            $node['children'][$s] = array('children' => array());
        }
        $node = &$node['children'][$s];
    }
    $node['name'] = $d['name'];
}
?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo __('Locations') ?>
    </a>
    <?php render_children($tree, $device, $current_action) ?>
</li>
<?php } ?>
