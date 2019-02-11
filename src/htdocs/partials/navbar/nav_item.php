<li class="nav-item">
    <a class="nav-link <?php echo ($action == array($currentController, $currentAction)) ? 'active' : ''; ?>" href="<?php echo l(...$action) ?>"><?php echo __($desc) ?></a>
</li>
