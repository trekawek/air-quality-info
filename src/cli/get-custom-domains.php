#!/usr/bin/env php
<?php
include('boot-cli.php');
$stmt = $this->mysqli->prepare("SELECT `fqdn` FROM `custom_domains`");
$stmt->execute();
$domains = array();
while ($row = $result->fetch_row()) {
    $domains[] = $row[0];
}
$stmt->close();
echo implode(',', $domains);
?>