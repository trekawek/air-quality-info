<li class="<?php echo $liClass ?>">
<?php if (is_array($action)): ?>
    <a class="<?php echo $aClass ?> <?php echo ($action == array($currentController, $currentAction)) ? 'active' : ''; ?>" href="<?php echo l(...$action) ?>"><?php echo __($desc) ?></a>
<?php elseif (is_string($action)): ?>
    <a class="<?php echo $aClass ?> <?php echo ($action == $uri) ? 'active' : ''; ?>" href="<?php echo $action ?>"><?php echo __($desc) ?></a>
<?php endif ?>
</li>
