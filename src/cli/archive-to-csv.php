#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

$csvModel = $diContainer->injectClass('\AirQualityInfo\Model\CsvModel');

$stmt = $pdo->prepare("SELECT `id` FROM `devices`");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $deviceId = $row[0];
    echo "Archiving records for device $deviceId\n";

    $recordStmt = $pdo->prepare("SELECT * FROM `records` WHERE `device_id` = ? ORDER BY `timestamp`");
    $recordStmt->execute([$deviceId]);
    $records = array();
    while ($record = $recordStmt->fetch()) {
        $records[] = $record;
        if (count($records) > 1024) {
            $csvModel->storeRecords($deviceId, $records);
            $records = array();
        }
    }
    $recordStmt->closeCursor();
    if (!empty($records)) {
        $csvModel->storeRecords($deviceId, $records);
    }
}
$stmt->closeCursor();

?>