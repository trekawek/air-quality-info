#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

$csvModel = $diContainer->injectClass('\AirQualityInfo\Model\CsvModel');

$stmt = $mysqli->prepare("SELECT `id` FROM `devices`");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_row()) {
    $deviceId = $row[0];
    echo "Archiving records for device $deviceId\n";

    $recordStmt = $mysqli->prepare("SELECT * FROM `records` WHERE `device_id` = ? ORDER BY `timestamp`");
    $recordStmt->bind_param('i', $deviceId);
    $recordStmt->execute();
    $recordResult = $recordStmt->get_result();
    $records = array();
    while ($record = $recordResult->fetch_assoc()) {
        $records[] = $record;
        if (count($records) > 1024) {
            $csvModel->storeRecords($deviceId, $records);
            $records = array();
        }

    }
    if (!empty($records)) {
        $csvModel->storeRecords($deviceId, $records);
    }
}
$stmt->close();

?>