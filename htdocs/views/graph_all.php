<?php include('partials/head.php'); ?>
<?php
$graphs = array();
if (isset($device['value_mapping']['pm10']) || isset($device['value_mapping']['pm_25'])) {
  $graphs['pm'] = 'PM';
}
if (isset($device['value_mapping']['temperature'])) {
  $graphs['temperature'] = 'Temperatura';
}
if (isset($device['value_mapping']['humidity'])) {
  $graphs['humidity'] = 'Wilgotność';
}
if (isset($device['value_mapping']['pressure'])) {
  $graphs['pressure'] = 'Ciśnienie';
}
foreach($graphs as $type => $name):
?>
      <div class="row">
        <div class="col-md-12">
          <h3><?php echo $name; ?></h3>
        </div>
<?php foreach(array('day' => 'Wykres dzienny', "week" => 'Wykres tygodniowy', "month" => 'Wykres miesięczny', "year" => 'Wykres roczny') as $range => $range_name): ?>
        <div class="col-md-6">
          <?php echo $range_name; ?>
          <a href="<?php echo l($device, 'graph.png', array('type' => $type, 'range' => $range, 'size' => 'large')); ?>">
            <img src="<?php echo l($device, 'graph.png', array('type' => $type, 'range' => $range, 'size' => 'default')); ?>" class="graph" />
          </a>
        </div>
<?php endforeach; ?>
      </div>
<?php endforeach; ?>
<?php include('partials/tail.php'); ?>