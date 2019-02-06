<h2>
    <?php if ($averages['max_level'] !== null): ?>
    <span class="badge index-cat-<?php echo $averages['max_level']; ?>">
        <?php echo POLLUTION_LEVELS[$averages['max_level']]['name']; ?>
    </span>
    <?php else: ?>
    <span class="badge badge-dark">
        <?php echo __('There are no data') ?>
    </span>
    <?php endif ?>
</h2>
