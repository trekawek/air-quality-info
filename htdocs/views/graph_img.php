<?php
$graph = generate_graph($device['esp8266id'],
  isset($_GET['type']) ? $_GET['type'] : 'pm',
  isset($_GET['range']) ? $_GET['range'] : 'day',
  isset($_GET['size']) ? $_GET['size'] : 'default'
);
if ($graph === null) {
  http_response_code(404);
  die();
}

header("Content-type: image/png");
header('Pragma: public');
header('Cache-Control: max-age=300');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 300));

echo $graph;
?>