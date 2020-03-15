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
<?php include('partials/weather.php'); ?>
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