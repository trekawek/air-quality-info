<?php
namespace AirQualityInfo\Model\Migration;

class MadaviMigrator {

    const BATCH_SIZE = 1024;

    const SENSOR_URL = 'https://api-rrd.madavi.de';

    const MAPPING = array(
        'Temp' => 'temperature',
        'Humidity' => 'humidity'
    );

    private $updater;

    private $deviceModel;

    private $batch = array();

    private $device;

    public function __construct(\AirQualityInfo\Model\Updater $updater,
        \AirQualityInfo\Model\DeviceModel $deviceModel) {
        $this->updater = $updater;
        $this->deviceModel = $deviceModel;
    }

    public function migrate($device) {
        $this->device = $device;
        $this->device['mapping'] = $this->deviceModel->getMappingAsAMap($device['id']);

        $index = file_get_contents(MadaviMigrator::SENSOR_URL.'/csvfiles.php?sensor=esp8266-'.$device['esp8266_id']);
        $files = array();
        foreach (explode("\n", $index) as $line) {
            $line = explode("'", $line);
            if (count($line) != 5) {
                continue;
            }
            $file = $line[3];
            $ext = substr($file, -4, 4);
            if ($ext === '.zip') {
                $this->processZipUrl(MadaviMigrator::SENSOR_URL.'/'.$file);
            } else {
                $this->processCsvUrl(MadaviMigrator::SENSOR_URL.'/'.$file);
            }
        }
        $this->flushBatch();
    }

    private function processZipUrl($url) {
        $src = fopen($url, 'r');
        $localZip = tempnam("/tmp", "madavi_zip");
        $dst = fopen($localZip, 'w');
        stream_copy_to_stream($src, $dst);
        fclose($dst);
        fclose($src);

        $zip = new \ZipArchive();
        if ($zip->open($localZip) == TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fp = $zip->getStream($filename);
                $this->processCsv($url.'#'.$filename, $fp);
                fclose($fp);
            }
        }

        unlink($localZip);
    }

    private function processCsvUrl($url) {
        $fp = fopen($url, 'r');
        if ($fp) {
            $this->processCsv($url, $fp);
            fclose($fp);
        } else {
            echo "Can't open $url\n";
            flush();
        }
    }

    private function processCsv($url, $fp) {
        $utc = new \DateTimeZone('UTC');

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
            $time = \DateTime::createFromFormat('Y/m/d H:i:s', $row['Time'], $utc)->getTimestamp();
            foreach (MadaviMigrator::MAPPING as $csvKey => $jsonKey) {
                if (isset($row[$csvKey])) {
                    $row[$jsonKey] = $row[$csvKey];
                    unset($row[$csvKey]);
                }
            }
            $this->addToBatch($time, $row);
            $i++;
        }
        echo "Read $i entries.\n";
        flush();
    }

    private function addToBatch($time, $data) {
        $this->batch[] = array('time' => $time, 'data' => $data);
        if (count($this->batch) >= MadaviMigrator::BATCH_SIZE) {
            $this->flushBatch();
        }
    }

    private function flushBatch() {
        if (!empty($this->batch)) {
            echo "Writing to database ".count($this->batch)." entries...\n";
            flush();
            $this->updater->insertBatch($this->device, $this->batch);
            $this->batch = array();
            echo "Done\n";
            flush();
        }
    }

}
?>
