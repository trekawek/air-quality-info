<?php if (CONFIG['store_json_payload'] && count($jsonUpdates) > 0): ?>
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
      <?php foreach($jsonUpdates as $t => $data): ?>
        <tr>
          <td><?php echo date("Y-m-d H:i:s", $t); ?></td>
          <td><?php echo substr($data, 0, 64) ?><a href="<?php echo l('debug', 'get_json', null, array('timestamp' => $t)) ?>">...</a></td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif ?>