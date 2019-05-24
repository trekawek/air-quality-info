<?php if (count($devices) > 1): ?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo __('Locations') ?>
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
    <?php foreach ($devices as $d): ?>
        <?php if ($d['hidden']) { continue; } ?>
        <li class="dropdown">
            <a class="dropdown-item <?php echo ($d['id'] == $currentDevice['id']) ? 'active' : '' ?>"
            href="<?php 
            if ($currentController == 'main' && $currentAction == 'all') {
                echo l('main', 'index', $d);
            } else {
                echo l($currentController, $currentAction, $d);
            }
            ?>">
            <?php echo $d['description'] ?>
            </a>
        </li>
    <?php endforeach ?>

    <li class="dropdown-divider"></li>
    <li class="dropdown">
        <a class="dropdown-item <?php echo ($currentController == 'main' && $currentAction == 'all') ? 'active' : '' ?>"
           href="<?php echo l('main', 'all') ?>">
           <?php echo __('Show all') ?>
        </a>
    </li>
    </ul>
</li>
<?php endif ?>