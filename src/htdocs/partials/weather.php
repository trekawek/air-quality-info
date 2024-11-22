            <ul class="list-group">
                <li class="list-group-item">
                <i class="wi wi-thermometer weather-icons-size"></i>
                <span class="weather-icons-text-temp" title="<?php echo __('Temperature') ?>">
                    <?php echo round($homeWidget['temperature'], 1); ?><span class="weather-icons-text-light">&deg;C</span>
                </span>
                </li>
                <li class="list-group-item">
                <i class="wi wi-barometer weather-icons-size"></i>
                <span class="weather-icons-text" title="<?php echo __('Atmospheric pressure') ?>">
                    <?php echo round($homeWidget['pressure'], 0); ?> <span class="weather-icons-text-light">hPa</span>
                </span>
                &nbsp;&nbsp;
                <i class="wi wi-humidity weather-icons-size"></i>
                <span class="weather-icons-text" title="<?php echo __('Humidity') ?>">
                    <?php echo round($homeWidget['humidity'], 0); ?>%
                </span>
                </li>
<?php if($homeWidget['wind_speed'] !== null || $homeWidget['rainfall'] !== null): ?>
                <li class="list-group-item">
<?php if($homeWidget['wind_speed'] !== null): ?>
                <i class="wi wi-strong-wind weather-icons-size"></i>
                <span class="weather-icons-text" title="<?php echo __('Wind speed') ?>">
                    <?php echo round($homeWidget['wind_speed'], 1); ?> km/h
                </span>
                &nbsp;&nbsp;
<?php endif; ?>
<?php if($homeWidget['rainfall'] !== null): ?>
                <i class="wi wi-raindrops weather-icons-size"></i>
                <span class="weather-icons-text" title="<?php echo __('Rainfall') ?>">
                    <?php echo round($homeWidget['rainfall'], 0); ?> mm
                </span>
                </li>
<?php endif ?>
<?php endif ?>
            </ul>