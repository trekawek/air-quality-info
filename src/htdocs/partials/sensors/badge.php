<small>
<a class="text-dark" href="https://www.airqualitynow.eu/about_indices_definition.php"><?php echo __('Pollution level') ?></a>:</small>
<h2>
    <?php if ($averages['max_level'] !== null): ?>
    <span class="badge index-cat-<?php echo $averages['max_level']; ?>">
        <?php echo __(\AirQualityInfo\Lib\PollutionLevel::POLLUTION_LEVELS[$averages['max_level']]['name']); ?>
    </span>
    <?php else: ?>
    <span class="badge badge-dark">
        <?php echo __('There are no data') ?>
    </span>
    <?php endif ?>
</h2>
