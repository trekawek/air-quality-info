<?php
$data = get_data($device['esp8266id'],
  isset($_GET['type']) ? $_GET['type'] : 'pm',
  isset($_GET['range']) ? $_GET['range'] : 'day'
);
if ($data === null) {
  http_response_code(404);
  die();
}

header('Content-type:application/json;charset=utf-8');
header('Pragma: public');
header('Cache-Control: max-age=300');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 300));

echo json_encode($data);
?>