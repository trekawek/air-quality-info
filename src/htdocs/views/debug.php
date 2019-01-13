<?php
date_default_timezone_set('Europe/Warsaw');
$sensors = $dao->getLastData();

$json_path = 'data/'.$device['esp8266id'].'.json';
if (file_exists($json_path)) {
  $json = json_decode(file_get_contents($json_path), true);
  $last_update = filemtime($json_path);
} else {
  $json = null;
  $last_update = $sensors['last_update'];
}
unset($sensors['last_update']);
?><?php include('partials/head.php'); ?>
<dl class="row">
  <dt class="col-md-3 offset-md-2"><?php echo __('Sensor ID') ?></dt>
  <dd class="col-md-5"><?php echo $device['esp8266id'] ?></dd>

<?php if ($json): ?>
  <dt class="col-md-3 offset-md-2"><?php echo __('Firmware version') ?></dt>
  <dd class="col-md-5"><?php echo $json['software_version'] ?></dd>
<?php endif ?>

  <dt class="col-md-3 offset-md-2"><?php echo __('Last update') ?></dt>
  <dd class="col-md-5"><?php echo date("Y-m-d H:i:s", $last_update); ?></dd>

  <dt class="col-md-3 offset-md-2"><?php echo __('Values stored in RRD') ?></dt>
  <dd class="col-md-5">
    <dl class="row">
      <?php foreach($sensors as $key => $value): ?>
        <dt class="col-md-8"><?php echo $key ?></dt>
        <dd class="col-md-4"><?php echo $value ?></dd>
      <?php endforeach ?>
    </dl>
  </dd>

  <?php if ($json): ?>
  <dt class="col-md-3 offset-md-2"><?php echo __('Received JSON') ?></dt>
  <dd class="col-md-5">
    <dl class="row">
      <?php foreach($json['sensordatavalues'] as $e): ?>
        <dt class="col-md-8"><?php echo $e['value_type'] ?></dt>
        <dd class="col-md-4"><?php echo $e['value'] ?></dd>
      <?php endforeach ?>
    </dl>
  </dd>
  <?php endif ?>

</dl>

<?php include('partials/tail.php'); ?>