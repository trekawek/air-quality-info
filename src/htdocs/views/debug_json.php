<?php
if (isset($_GET['ts'])):
  $json = $dao->getJsonUpdate($_GET['ts']);
  if ($json === null) {
    http_response_code(404);
    die();
  }
  header('Content-type:application/json;charset=utf-8');
  echo $json;
else:
  $json_updates = $dao->getJsonUpdates();
?>
<?php include('partials/head.php'); ?>
<?php if (CONFIG['store_json_payload'] && count($json_updates) > 0): ?>
<div class="row">
  <div class="col-md-8 offset-md-2">
    <table class="table">
      <thead>
        <tr>
          <th scope="col"><?php echo __('Time') ?></th>
          <th scope="col"><?php echo __('Data') ?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($json_updates as $t => $data): ?>
        <tr>
          <td><?php echo date("Y-m-d H:i:s", $t); ?></td>
          <td><?php echo substr($data, 0, 64) ?><a href="<?php echo l($device, 'debug/json', array('ts' => $t)) ?>">...</a></td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif ?>
<?php include('partials/tail.php'); ?>
<?php endif ?>