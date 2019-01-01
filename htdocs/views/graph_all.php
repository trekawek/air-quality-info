<?php include('partials/head.php'); ?>
<?php foreach(array('pm' => 'PM'/*, "temperature" => 'Temperatura', "humidity" => 'Wilgotność', "pressure" => 'Ciśnienie'*/) as $type => $name): ?>
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