<?php
require_once('config.php');
require_once('lib/rrd.php');

$graph = generate_graph($rrd_file,
  isset($_GET['type']) ? $_GET['type'] : 'pm',
  isset($_GET['range']) ? $_GET['range'] : 'day',
  isset($_GET['size']) ? $_GET['size'] : 'default'
);

header("Content-type: image/png");
header('Pragma: public');
header('Cache-Control: max-age=300');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 300));

echo $graph;

?>