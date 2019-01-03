<?php
function get_rrd_path($esp8266id) {
  return __DIR__ . "/../data/${esp8266id}.rrd";
}

function get_sensor_data($esp8266id) {
  $rrd_file = get_rrd_path($esp8266id);
  $data = rrd_lastupdate($rrd_file);
  $sensors = array('last_update' => $data['last_update']);
  for ($i = 0; $i < $data['ds_cnt']; $i++) {
    $sensors[$data['ds_navm'][$i]] = $data['data'][$i];
  }
  return $sensors;
}

function create_rrd($esp8266id) {
  $rrd_file = get_rrd_path($esp8266id);
  rrd_create($rrd_file, array(
    '--step=3m',
    'DS:PM25:GAUGE:5m:0:1000',
    'DS:PM10:GAUGE:5m:0:1000',
    'DS:TEMPERATURE:GAUGE:5m:-100:100',
    'DS:PRESSURE:GAUGE:5m:900:1100',
    'DS:HUMIDITY:GAUGE:5m:0:100',
    'RRA:AVERAGE:0.5:3m:24h',
    'RRA:AVERAGE:0.5:15m:35d',
    'RRA:AVERAGE:0.5:12h:1y'
  ));
}

function update_rrd($esp8266id, $time, $pm25, $pm10, $temp, $press, $hum) {
  $rrd_file = get_rrd_path($esp8266id);
  if (!file_exists($rrd_file)) {
    create_rrd($esp8266id);
  }
  rrd_update($rrd_file, array("${time}:${pm25}:${pm10}:${temp}:${press}:${hum}"));
}

function generate_graph($esp8266id, $type = 'pm', $range = 'day', $size = 'default') {
  $rrd_file = get_rrd_path($esp8266id);
  $options = array();

  switch ($size) {
    case 'large':
    array_push($options,
      '--width=800',
      '--height=200'
    );
    break;

    case 'mid':
    array_push($options,
      '--width=600',
      '--height=150'
    );
    break;

    case 'default':
    default:
    array_push($options,
      '--width=400',
      '--height=100'
    );
    break;
  }

  switch ($range) {
    case 'week':
    array_push($options, "--start=now-7d");
    break;

    case 'month':
    array_push($options, "--start=now-1m");
    break;

    case 'year':
    array_push($options, "--start=now-1y");
    break;

    case 'day':
    default:
    array_push($options, "--start=now-24h");
    break;
  }

  switch ($type) {
    case 'temperature':
    array_push($options,
      '--vertical-label=°C',
      "DEF:Temperatura=${rrd_file}:TEMPERATURE:AVERAGE",
      'LINE2:Temperatura#CC3118'
    );
    break;

    case 'pressure':
    array_push($options,
      '--vertical-label=hPa',
      "DEF:Ciśnienie=${rrd_file}:PRESSURE:AVERAGE",
      'LINE2:Ciśnienie#CC3118'
    );
    break;

    case 'humidity':
    array_push($options,
      '--vertical-label=%',
      "DEF:Wilgotność=${rrd_file}:HUMIDITY:AVERAGE",
      'LINE2:Wilgotność#CC3118'
    );
    break;

    case 'pm':
    default:
    array_push($options,
      '--vertical-label=µg/m³',
      "DEF:PM25=${rrd_file}:PM25:AVERAGE",
      "DEF:PM10=${rrd_file}:PM10:AVERAGE",
      'AREA:PM10#EA644A:PM10',
      'AREA:PM25#EC9D48:PM2.5',
      'LINE2:PM10#CC3118',
      'LINE2:PM25#CC7016',
      'HRULE:'.PM10_LIMIT.'#CC3118:Limit PM10:dashes=5,5',
      'HRULE:'.PM25_LIMIT.'#CC7016:Limit PM2.5:dashes=5,5'
    );
    break;
  }

  $graphObj = new RRDGraph('-');
  $graphObj->setOptions($options);
  $res = $graphObj->saveVerbose();
  return $res['image'];
}
?>