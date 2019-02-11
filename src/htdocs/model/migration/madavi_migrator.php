<?php
class MadaviMigrator {

    const SENSOR_URL = 'https://www.madavi.de/sensor';

    const MAPPING = array(
        'Temp' => 'temperature',
        'Humidity' => 'humidity'
    );

    private $dao;

    private $currentLocale;

    private $updater;

    public function __construct($dao, $updater) {
        $this->dao = $dao;
        $this->updater = $updater;
    }

    public function migrate($device) {
        $index = file_get_contents(MadaviMigrator::SENSOR_URL.'/csvfiles.php?sensor=esp8266-'.$device['esp8266id']);
        $files = array();
        foreach (explode("\n", $index) as $line) {
            $line = explode("'", $line);
            if (count($line) != 5) {
                continue;
            }
            $file = $line[3];
            $ext = substr($file, -4, 4);
            if ($ext === '.zip') {
                $this->processZipUrl($device, MadaviMigrator::SENSOR_URL.'/'.$file);
            } else {
                $this->processCsvUrl($device, MadaviMigrator::SENSOR_URL.'/'.$file);
            }
        }
    }

    private function processZipUrl($device, $url) {
        $src = fopen($url, 'r');
        $localZip = tempnam("/tmp", "madavi_zip");
        $dst = fopen($localZip, 'w');
        stream_copy_to_stream($src, $dst);
        fclose($dst);
        fclose($src);

        $zip = new ZipArchive();
        if ($zip->open($localZip) == TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fp = $zip->getStream($filename);
                $this->processCsv($device, $url.'#'.$filename, $fp);
                fclose($fp);
            }
        }

        unlink($localZip);
    }

    private function processCsvUrl($device, $url) {
        $fp = fopen($url, 'r');
        $this->processCsv($device, $url, $fp);
        fclose($fp);
    }

    private function processCsv($device, $url, $fp) {
        $utc = new DateTimeZone('UTC');

        echo "Processing file $url\n";
        flush();

        $header = explode(';', fgets($fp, 4096));
        $i = 0;
        while (($line = fgets($fp, 4096)) !== false) {
            $line = explode(';', trim($line));
            $row = array();
            for ($i = 0; $i < count($header); $i++) {
                $val = isset($line[$i]) ? $line[$i] : null;
                if ($val !== null && $val !== '') {
                    $row[$header[$i]] = $val;
                }
            }
            $time = DateTime::createFromFormat('Y/m/d H:i:s', $row['Time'], $utc)->getTimestamp();
            foreach (MadaviMigrator::MAPPING as $csvKey => $jsonKey) {
                $row[$jsonKey] = $row[$csvKey];
                unset($row[$csvKey]);
            }
            $this->updater->insert($device, $time, $row);
            $i++;
        }
        echo "Added $i entries.\n";
        flush();
    }

}
?>