<dl class="row">
  <dt class="col-md-3 offset-md-2"><?php echo __('Sensor ID') ?></dt>
  <dd class="col-md-5"><?php echo $currentDevice['esp8266id'] ?></dd>

  <dt class="col-md-3 offset-md-2"><?php echo __('Last update') ?></dt>
  <dd class="col-md-5"><?php echo date("Y-m-d H:i:s", $lastUpdate); ?></dd>

  <dt class="col-md-3 offset-md-2"><?php echo __('Values') ?></dt>
  <dd class="col-md-5">
    <dl class="row">
      <?php foreach($sensors as $key => $value): ?>
        <dt class="col-md-8"><?php echo $key ?></dt>
        <dd class="col-md-4"><?php echo $value ?></dd>
      <?php endforeach ?>
    </dl>
  </dd>
</dl>