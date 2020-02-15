<div class="row homewidget">
  <div class="col-md-8 offset-md-2">

    <div class="row">
      <div class="col-md-6">
        <ul class="list-group">
          <li class="list-group-item text-center">
            <h3 class=""><?php echo __('Air quality') ?></h3>
          </li>
          <li class="list-group-item text-center">
            <i class="fa <?php echo $homeWidget['locale']['icon'] ?> fa-5x air-quality-<?php echo $homeWidget['level'] ?>"></i>
            <h3 class="air-quality-<?php echo $homeWidget['level'] ?>"><?php echo $homeWidget['locale']['label'] ?></h3>
          </li>
        </ul>
      </div>
      

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
                    <?php echo number_format_locale($homeWidget['temperature'], 1); ?><span class="weather-icons-text-light">&deg;C</span>
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
            </li>
        </ul>
      </div>
    </div>

    <div class="row">
      <?php foreach ($homeWidget['locale']['recommendations'] as $r): ?>
        <div class="col-md-6">
        <ul class="list-group">
          <li class="list-group-item text-center">
            <span class="strip"><i class="<?php echo $r['icon']; ?> <?php echo $r['color']; ?>"></i>
              <span class="strip-recommended">
                <strong><?php echo $r['label']; ?></strong>
                <br />
                <span class="strip-recommended-description"><?php echo $r['description']; ?></span>
              </span>
            </span>
          </li>
        </ul>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</div>