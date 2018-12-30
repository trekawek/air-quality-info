<?php
require('config.php');

$start = null;
switch ($_GET['range']) {
  case 'week':
  $start = "now-7d";
  break;

  case 'month':
  $start = "now-1m";
  break;

  case 'year':
  $start = "now-1y";
  break;

  case 'day':
  default:
  $start = "now-24h";
  break;
}

$width = '400';
$height = '100';

if (isset($_GET['size']) && $_GET['size'] == 'large') {
  $width = '800';
  $height = '200';
}
  
$options = array(
  '--start='.$start,
  '--end=now-3min',
  '--vertical-label=µg/m³',
  '--width='.$width,
  '--height='.$height,
  'DEF:PM25=data.rrd:PM25:AVERAGE',
  'DEF:PM10=data.rrd:PM10:AVERAGE',
  'AREA:PM10#EA644A:PM10',
  'AREA:PM25#EC9D48:PM2.5',
  'LINE2:PM10#CC3118',
  'LINE2:PM25#CC7016'
);

$graphObj = new RRDGraph('-');
$graphObj->setOptions($options);
$res = $graphObj->saveVerbose();

header("Content-type: image/png");
header('Pragma: public');
header('Cache-Control: max-age=300');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 300));

echo $res['image'];
?>