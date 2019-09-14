<li class="nav-item">
<?php if (is_array($action)): ?>
    <a class="nav-link <?php echo ($action == array($currentController, $currentAction)) ? 'active' : ''; ?>" href="<?php echo l(...$action) ?>"><?php echo __($desc) ?></a>
<?php elseif (is_string($action)): ?>
    <a class="nav-link <?php echo $uri ?> <?php echo ($action == $uri) ? 'active' : ''; ?>" href="<?php echo $action ?>"><?php echo __($desc) ?></a>
<?php endif ?>
</li>
