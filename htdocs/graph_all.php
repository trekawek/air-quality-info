<?php include('partials/head.php'); ?>
    <div class="container">
<?php foreach(array('pm' => 'PM'/*, "temperature" => 'Temperatura', "humidity" => 'Wilgotność', "pressure" => 'Ciśnienie'*/) as $type => $name): ?>
      <div class="row">
        <div class="col-md-12">
          <h3><?php echo $name; ?></h3>
        </div>
<?php foreach(array('day' => 'Wykres dzienny', "week" => 'Wykres tygodniowy', "month" => 'Wykres miesięczny', "year" => 'Wykres roczny') as $range => $range_name): ?>
        <div class="col-md-6">
          <?php echo $range_name; ?>
          <a href="graph_img.php?type=<?php echo $type; ?>&range=<?php echo $range; ?>&size=large">
            <img src="graph_img.php?type=<?php echo $type; ?>&range=<?php echo $range; ?>" class="graph" />
          </a>
        </div>
<?php endforeach; ?>
      </div>
<?php endforeach; ?>
      <div class="row">
        <div class="col-md-12">
          <a class="btn btn-primary" href="index.php" role="button">Powrót</a>
        </div>
      </div>
    </div> <!-- /container -->
<?php include('partials/tail.php'); ?>