<?php if (count(CONFIG['devices']) > 1) {
require_once('partials/navbar/children.php');
?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo __('Locations') ?>
    </a>
    <?php renderChildren($deviceTree['tree'], $deviceGroupId) ?>
</li>
<?php } ?>