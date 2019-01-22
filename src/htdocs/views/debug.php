<?php
date_default_timezone_set('Europe/Warsaw');
$sensors = $dao->getLastData();
$json_updates = $dao->getJsonUpdates();
$last_update = $sensors['last_update'];
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
</dl>

<?php if (CONFIG['store_json_payload'] && count($json_updates) > 0): ?>
<div class="row">
  <div class="col-md-8 offset-md-2">
    <h4>Received JSONs</h4>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Time</th>
          <th scope="col">Data</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($json_updates as $ts => $data): ?>
        <tr>
          <td><?php echo date("Y-m-d H:i:s", $ts); ?></td>
          <td><?php echo substr($data, 0, 64) ?>...</td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif ?>
<?php include('partials/tail.php'); ?>