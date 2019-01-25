<?php
function render_children($node, $device, $current_action) {
?>
<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
    <?php foreach ($node['children'] as $d => $n): ?>
    <li class="dropdown">
        <?php if (isset($n['name'])): ?>
        <a class="dropdown-item <?php echo ($n['name'] == $device['name']) ? 'active' : '' ?>" href="<?php echo l(array('name' => $n['name']), $current_action) ?>">
            <?php echo $d ?>
        </a>
        <?php else: ?>
        <a class="dropdown-item" href="#">
            <?php echo $d ?>
        </a>
        <?php endif ?>
        <?php
        if (count($n['children']) > 0) {
            render_children($n, $device, $current_action);
        }
        ?>
    </li>
    <?php endforeach ?>
</ul>
<?php } ?>