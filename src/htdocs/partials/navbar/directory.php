<?php
function displayDirectory($node, $currentController, $currentAction, $currentDevice, $nodeId = null) {
    global $currentController, $currentAction;
?>
<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
<?php if (empty($node['children'])): ?>
<li class="dropdown">
    <a class="dropdown-item" href="#">
        -
    </a>
</li>
<?php else: ?>
<?php foreach ($node['children'] as $n): ?>
    <li class="dropdown">
        <?php if (isset($n['device'])): ?>
            <?php if (($currentController == 'main' && $currentAction == 'all') || $currentController == 'map'): ?>
            <a class="dropdown-item" href="<?php echo l('main', 'index', $n['device']) ?>">
                <?php echo $n['description'] ?>
            </a>
            <?php else: ?>            
            <a class="dropdown-item <?php echo ($n['device']['id'] === $currentDevice['id']) ? 'active' : '' ?>" href="<?php echo l($currentController, $currentAction, $n['device']) ?>">
                <?php echo $n['description'] ?>
            </a>
            <?php endif ?>
        <?php else: ?>
        <a class="dropdown-item" href="#">
            <?php echo $n['description'] ?>
        </a>
        <?php echo displayDirectory($n, $currentController, $currentAction, $currentDevice, $nodeId) ?>
        <?php endif ?>
    </li>
<?php endforeach ?>
<?php endif ?>
<li class="dropdown-divider"></li>
<li class="dropdown">
    <a class="dropdown-item <?php echo ($currentController == 'main' && $currentAction == 'all' && $nodeId == $node['id']) ? 'active' : '' ?>"
        href="<?php echo l('main', 'all', null, array('node_id' => $node['id'])) ?>">
        <?php echo __('Show all') ?>
    </a>
</li>
</ul>
<?php } ?>