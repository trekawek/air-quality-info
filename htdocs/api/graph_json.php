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
echo json_encode($data);
?>