<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
    <small><span class="smallTitle"><?php echo __('Average') ?></span>:
    (<?php
        foreach (array('0' => __('Last'), '0.25' => '15m', '1' => '1h', '24' => '24h') as $value => $name) {
          if ($currentAvgType == $value) {
            echo "$name";
          } else if ($currentController == 'main' && $currentAction == 'all') {
            echo "<a href=\"".l('main', 'all', null, array('node_id' => $nodeId), array('avgType' => $value))."\">$name</a>";
          } else {
            echo "<a href=\"#\" class=\"switch-avg-type\" data-avg-type=\"$value\">$name</a>";
          }
          if ($value != '24') {
            echo " / ";
          }
      }?>)
    </small>
  </div>
</div>
