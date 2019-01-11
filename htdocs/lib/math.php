<?php
function transform_to_walking_average($data, $walking_average_hours) {
  $data_array = array();
  foreach ($data as $ts => $v) {
    $data_array[] = array('ts' => $ts, 'v' => $v);
  }
  $data = $data_array;
  $result = array();
  $data_size = count($data);

  $j = null;
  $sum = 0;
  $count = 0;
  for ($j = 0; $j < $data_size; $j++) {
    if (($data[$j]['ts'] - $data[0]['ts']) >= $walking_average_hours * 60 * 60) {
      break;
    }
    if ($data[$j]['v'] != null) {
      $sum += $data[$j]['v'];
      $count++;
    }
  }

  $i = 0;
  for ($j--; $j < $data_size; $j++) {
    if ($data[$j]['v'] == null) {
      $result[$data[$j]['ts']] = null;
    } else {
      $sum += $data[$j]['v'];
      $count++;
      $result[$data[$j]['ts']] = $sum / $count;
    }
    if ($data[$i]['v'] != null) {
      $sum -= $data[$i]['v'];
      $count--;
    }
    $i++;
  }
  return $result;
}
?>