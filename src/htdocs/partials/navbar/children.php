<?php
function render_children($node, $device, $current_action, $deviceGroupId = null) {
?>
<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
    <?php foreach ($node['children'] as $d => $n): ?>
    <li class="dropdown">
        <?php if (isset($n['name'])): ?>
        <a class="dropdown-item <?php echo ($n['name'] == $device['name'] && $deviceGroupId === null) ? 'active' : '' ?>" href="<?php echo l(array('name' => $n['name']), $current_action) ?>">
            <?php echo $d ?>
        </a>
        <?php else: ?>
        <a class="dropdown-item" href="#">
            <?php echo $d ?>
        </a>
        <?php endif ?>
        <?php
        if (count($n['children']) > 0) {
            render_children($n, $device, $current_action, $deviceGroupId);
        }
        ?>
    </li>
    <?php endforeach ?>
    <li class="dropdown-divider"></li>
    <li><a class="dropdown-item <?php echo ($deviceGroupId == $node['id']) ? 'active' : '' ?>" href="/all_sensors/<?php echo $node['id'] ?>"><?php echo __('Show all') ?></a></li>
</ul>
<?php } ?>