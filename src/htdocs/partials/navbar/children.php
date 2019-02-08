<?php
function renderChildren($node, $deviceGroupId) {
    global $currentController, $currentAction, $currentDevice;
?>
<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
    <?php foreach ($node['children'] as $d => $n): ?>
    <li class="dropdown">
        <?php if (isset($n['name'])): ?>
        <a class="dropdown-item <?php echo ($n['name'] == $currentDevice['name'] && $deviceGroupId === null) ? 'active' : '' ?>"
           href="<?php echo l($currentController, $currentAction, array('name' => $n['name'])) ?>">
            <?php echo $d ?>
        </a>
        <?php else: ?>
        <a class="dropdown-item" href="#">
            <?php echo $d ?>
        </a>
        <?php endif ?>
        <?php
        if (count($n['children']) > 0) {
            renderChildren($n, $deviceGroupId);
        }
        ?>
    </li>
    <?php endforeach ?>
    <li class="dropdown-divider"></li>
    <li>
      <a class="dropdown-item <?php echo ($deviceGroupId == $node['id']) ? 'active' : '' ?>"
         href="<?php echo l('main', 'all', null, array('groupId' => $node['id'])) ?>">
             <?php echo __('Show all') ?>
      </a>
    </li>
</ul>
<?php } ?>