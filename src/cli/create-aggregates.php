#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

$recordModel = new \AirQualityInfo\Model\RecordModel($pdo);

$stmt = $pdo->prepare("TRUNCATE TABLE `aggregates`");
$stmt->execute();
$stmt->closeCursor();

$stmt = $pdo->prepare("SELECT `id` FROM `devices`");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $deviceId = $row['id'];
    echo "Creating aggregates for device $deviceId\n";
    $recordModel->createAggregates($deviceId, 0, time());
}
$stmt->closeCursor();

?>