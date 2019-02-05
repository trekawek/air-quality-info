<?php if (count(CONFIG['devices']) > 1) {
require_once('partials/navbar/children.php');
$tree = createDeviceTree()['tree'];
?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo __('Locations') ?>
    </a>
    <?php render_children($tree, $device, $route_name == 'all_sensors' ? 'index' : $current_action, $deviceGroupId) ?>
</li>
<?php } ?>
