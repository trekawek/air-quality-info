
<div class="col-md-6">
<ul class="list-group">
    <li class="list-group-item text-center">
    <h3 class=""><?php echo __('Weather') ?></h3>
    </li>

    <li class="list-group-item text-center" style="margin-top:-20px;">
    <ul class="list-group">
        <li class="list-group-item">
        <i class="wi wi-thermometer weather-icons-size"></i>
        <span class="weather-icons-text-temp" title="<?php echo __('Temperature') ?>">
            <?php echo round($aggregate['temperature'], 1); ?><span class="weather-icons-text-light">&deg;C</span>
        </span>
        </li>
        <li class="list-group-item">
        <i class="wi wi-barometer weather-icons-size"></i>
        <span class="weather-icons-text" title="<?php echo __('Atmospheric pressure') ?>">
            <?php echo round($aggregate['pressure'], 0); ?> <span class="weather-icons-text-light">hPa</span>
        </span>
        &nbsp;&nbsp;
        <i class="wi wi-humidity weather-icons-size"></i>
        <span class="weather-icons-text" title="<?php echo __('Humidity') ?>">
            <?php echo round($aggregate['humidity'], 0); ?>%
        </span>
        </li>
    </ul>
    </li>
</ul>
</div>