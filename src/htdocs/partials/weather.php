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
            </ul>