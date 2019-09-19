#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

$stmt = $pdo->prepare("SELECT `fqdn` FROM `custom_domains`");
$stmt->execute();
$domains = array();
while ($row = $stmt->fetch()) {
    $domains[] = $row['fqdn'];
}
$stmt->closeCursor();
echo implode(',', $domains);
?>