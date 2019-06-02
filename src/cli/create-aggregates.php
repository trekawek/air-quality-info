#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

$recordModel = new \AirQualityInfo\Model\RecordModel($mysqli);

$stmt = $mysqli->prepare("TRUNCATE TABLE `aggregates`");
$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("SELECT `id` FROM `devices`");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_row()) {
    $deviceId = $row[0];
    echo "Creating aggregates for device $deviceId\n";
    $recordModel->createAggregates($deviceId, 0, time());
}
$stmt->close();

?>