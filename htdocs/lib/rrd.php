<?php
function get_rrd_path($esp8266id) {
  return __DIR__ . "/../data/${esp8266id}.rrd";
}

function get_sensor_data($esp8266id) {
  $rrd_file = get_rrd_path($esp8266id);
  if (!file_exists($rrd_file)) {
    return array();
  }

  $data = rrd_lastupdate($rrd_file);
  $sensors = array('last_update' => $data['last_update']);
  for ($i = 0; $i < $data['ds_cnt']; $i++) {
    $sensors[$data['ds_navm'][$i]] = $data['data'][$i];
  }
  return $sensors;
}

function get_avg_sensor_data($esp8266id, $hours) {
  $rrd_file = get_rrd_path($esp8266id);
  if (!file_exists($rrd_file)) {
    return array();
  }
  $options = array(
    'AVERAGE',
    '--start=now-'.$hours.'h',
    "--resolution=3m",
    "--end=now");
  $result = rrd_fetch($rrd_file, $options);
  $data = $result['data'];
  foreach ($data as $name => $values) {
    $sum = 0;
    $count = 0;
    foreach ($values as $v) {
      if (!is_nan($v)) {
        $sum += $v;
        $count++;
      }
    }
    if ($count == 0) {
      $data[$name] = null;
    } else {
      $data[$name] = $sum / $count;
    }
  }
  
  $last_update = get_sensor_data($esp8266id);
  foreach ($data as $k => $v) {
    if ($v === null) {
      $data[$k] = $last_update[$k];
    }
  }

  return $data;
}

function create_rrd($esp8266id) {
  $rrd_file = get_rrd_path($esp8266id);
  $options = array(
    '--step=3m',
    'DS:PM25:GAUGE:5m:0:1000',
    'DS:PM10:GAUGE:5m:0:1000',
    'DS:TEMPERATURE:GAUGE:5m:-100:100',
    'DS:PRESSURE:GAUGE:5m:900:1100',
    'DS:HUMIDITY:GAUGE:5m:0:100',
    'DS:HEATER_TEMPERATURE:GAUGE:5m:-100:100',
    'DS:HEATER_HUMIDITY:GAUGE:5m:0:100',
    'RRA:AVERAGE:0.5:3m:2d',
    'RRA:AVERAGE:0.5:15m:2w',
    'RRA:AVERAGE:0.5:90m:62d',
    'RRA:AVERAGE:0.5:12h:2y'
  );
  if (file_exists($rrd_file)) {
    array_push($options, '--source='.$rrd_file);
  }
  if (!rrd_create($rrd_file, $options)) {
    error_log(rrd_error());
  }
}

function update_rrd($esp8266id, $time, $pm25, $pm10, $temp, $press, $hum, $heater_temp, $heater_hum) {
  $rrd_file = get_rrd_path($esp8266id);
  if (!file_exists($rrd_file)) {
    create_rrd($esp8266id);
  }
  $data = "${time}:${pm25}:${pm10}:${temp}:${press}:${hum}:${heater_temp}:${heater_hum}";
  rrd_update($rrd_file, array($data));
  return $data;
}

function get_data($esp8266id, $type = 'pm', $range = 'day', $walking_average_hours = null) {
  $rrd_file = get_rrd_path($esp8266id);
  if (!file_exists($rrd_file)) {
    return null;
  }

  $options = array('AVERAGE');
  switch ($range) {
    case 'week':
    if ($walking_average_hours !== null) {
      array_push($options, '--start=now-'.($walking_average_hours + 24 * 7).'h', "--resolution=15m");
    } else {
      array_push($options, '--start=now-7d', "--resolution=15m");
    }
    break;

    case 'month':
    array_push($options, "--start=now-1m", "--resolution=90m");
    break;

    case 'year':
    array_push($options, "--start=now-1y", "--resolution=12h");
    break;

    case 'day':
    default:
    if ($walking_average_hours !== null) {
      array_push($options, '--start=now-'.($walking_average_hours + 24).'h', "--resolution=3m");
    } else {
      array_push($options, '--start=now-24h', "--resolution=3m");
    }
    break;
  }
  array_push($options, "--end=now");

  $result = rrd_fetch($rrd_file, $options);
  $data = $result['data'];
  
  switch ($type) {
    case 'temperature':
    $fields = array('TEMPERATURE', 'HEATER_TEMPERATURE');
    break;

    case 'pressure':
    $fields = array('PRESSURE');
    break;

    case 'humidity':
    $fields = array('HUMIDITY', 'HEATER_HUMIDITY');
    break;

    case 'pm':
    $fields = array('PM10', 'PM25');
    default:
    break;
  }

  $data = array_intersect_key($data, array_flip($fields));

  foreach ($data as $k => $values) {
    foreach ($values as $ts => $v) {
      if (is_nan($v)) {
        $data[$k][$ts] = null;
      }
    }
  }

  if ($walking_average_hours !== null) {
    foreach ($data as $k => $values) {
      $data[$k] = transform_to_walking_average($values, 60 * 60 * $walking_average_hours);
    }
    $result['start'] += 60 * 60 * $walking_average_hours;
  }

  $result['data'] = $data;
  return $result;
}

function transform_to_walking_average($data, $walking_average_seconds) {
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
    if (($data[$j]['ts'] - $data[0]['ts']) >= $walking_average_seconds) {
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