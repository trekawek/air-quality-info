<?php
require('config.php');
require('lib/pollution_levels.php');

$options = array();

switch ($_GET['size']) {
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

switch ($_GET['range']) {
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

switch ($_GET['type']) {
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

header("Content-type: image/png");
header('Pragma: public');
header('Cache-Control: max-age=300');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 300));

echo $res['image'];
?>