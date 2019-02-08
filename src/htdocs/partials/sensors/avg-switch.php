<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
    <small><?php echo __('<a href="https://www.airqualitynow.eu/about_indices_definition.php">CAQI</a> index') ?>
    (<?php
        foreach (array('1' => '1h', '24' => '24h') as $value => $name) {
          if ($currentAvgType == $value) {
            echo "<strong>$name</strong>";
          } else if ($currentController == 'main' && $currentAction == 'all') {
            echo "<a href=\"".l('main', 'all', null, array('groupId' => $deviceGroupId), array('avgType' => $value))."\">$name</a>";
          } else {
            echo "<a href=\"#\" class=\"switch-avg-type\" data-avg-type=\"$value\">$name</a>";
          }
          if ($value != '24') {
            echo " / ";
          }
      }?>):
    </small>
  </div>
</div>
