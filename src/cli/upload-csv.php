#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

function removeDuplicates($fileName) {
    $records = array();

    if (!file_exists($fileName)) {
        return;
    }

    $fp = fopen($fileName, 'r');
    $header = fgets($fp); // header
    while(!feof($fp))  {
        $line = trim(fgets($fp));
        $r = explode(';', $line);
        $records[$r[0]] = $line;
    }
    fclose($fp);

    ksort($records, SORT_NUMERIC);

    $fp = fopen($fileName, 'w');
    fwrite($fp, $header);
    foreach ($records as $r) {
        fwrite($fp, $r."\n");
    }
    fclose($fp);
}

function upload($s3Client, $fileName, $s3Name) {
    echo "$fileName -> $s3Name\n";

    $objectExist = $s3Client->doesObjectExist(CONFIG['s3Bucket'], $s3Name);

    if ($objectExist) {
        echo "Downloading existing $s3Name\n";
        $tmpName = tempnam(sys_get_temp_dir(), str_replace('/', '_', $fileName));
        $s3Client->getObject(array(
            'Bucket' => CONFIG['s3Bucket'],
            'Key'    => $s3Name,
            'SaveAs' => $tmpName
        ));

        echo "Merging entries with $s3Name\n";
        $fp = fopen($fileName, 'a');
        $s3Fp = fopen($tmpName, 'r');
        fgets($s3Fp); // header
        while(!feof($s3Fp))  {
            fwrite($fp, fgets($s3Fp));
        }
        fclose($s3Fp);
        fclose($fp);
        unlink($tmpName);
    }
    
    removeDuplicates($fileName);

    echo "Uploading $s3Name\n";
    $s3Client->putObject(array(
        'Bucket'      => CONFIG['s3Bucket'],
        'Key'         => $s3Name,
        'SourceFile'  => $fileName
    ));

    $s3Client->waitUntil('ObjectExists', array(
        'Bucket' => CONFIG['s3Bucket'],
        'Key'    => $s3Name
    ));

    echo "Done\n";
}

echo "Starting upload-csv\n";

$s3Client = $diContainer->getBinding('s3Client');
while (true) {
    if (!file_exists(CONFIG['csv_root'])) {
        sleep(60 * 60);
        continue;
    }
    $rootDir = new RecursiveDirectoryIterator(CONFIG['csv_root']);
    $iterator = new RecursiveIteratorIterator($rootDir);
    $regexIterator = new RegexIterator($iterator, '/^.+\.csv$/i', RecursiveRegexIterator::GET_MATCH);

    $currentCsv = date('Y-m-d').".csv";
    foreach ($regexIterator as $name => $object) {
        $fileName = basename($name);
        if ($currentCsv == $fileName) {
            continue;
        }
        upload($s3Client, $name, substr($name, strlen(CONFIG['csv_root']) + 1));
        sleep(1);
        unlink($name);
    }

    // run process every hour
    sleep(60 * 60);
}
?>