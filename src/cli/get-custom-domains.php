#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

$stmt = $mysqli->prepare("SELECT `fqdn` FROM `custom_domains`");
$stmt->execute();
$result = $stmt->get_result();
$domains = array();
while ($row = $result->fetch_row()) {
    $domains[] = $row[0];
}
$stmt->close();
echo implode(',', $domains);
?>