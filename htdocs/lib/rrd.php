<?php
function get_sensor_data($rrd_file) {
  $data = rrd_lastupdate($rrd_file);
  $sensors = array('last_update' => $data['last_update']);
  for ($i = 0; $i < $data['ds_cnt']; $i++) {
    $sensors[$data['ds_navm'][$i]] = $data['data'][$i];
  }
  return $sensors;
}  
?>