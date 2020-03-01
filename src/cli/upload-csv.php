#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

echo "Starting upload-csv\n";

$rootDir = new RecursiveDirectoryIterator(CONFIG['csv_root']);
$iterator = new RecursiveIteratorIterator($rootDir);
foreach ($iterator as $name => $object) {
    echo $name."\n";
}
?>