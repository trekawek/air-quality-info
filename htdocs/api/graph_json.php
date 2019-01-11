<?php
$data = $dao->getHistoricData(
  isset($_GET['type']) ? $_GET['type'] : 'pm',
  isset($_GET['range']) ? $_GET['range'] : 'day',
  isset($_GET['ma_h']) ? $_GET['ma_h'] : null
);
if ($data === null) {
  http_response_code(404);
  die();
}

header('Content-type:application/json;charset=utf-8');
echo json_encode($data);
?>