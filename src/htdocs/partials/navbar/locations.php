<?php include('directory.php'); ?>
<?php if ($displayLocations): ?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo __('Locations') ?>
    </a>
    <?php displayDirectory($deviceTree, $currentController, $currentAction, $currentDevice, isset($nodeId) ? $nodeId : null) ?>
</li>
<?php endif ?>