<?php
namespace AirQualityInfo\Model;

class CsvModel {

    const FIELDS = array('timestamp', 'pm25','pm10','temperature','pressure','humidity','heater_temperature','heater_humidity');

    private $deviceModel;

    private $userModel;

    private $space;

    public function __construct(DeviceModel $deviceModel, UserModel $userModel, \SpacesConnect $space) {
        $this->deviceModel = $deviceModel;
        $this->userModel = $userModel;
        $this->space = $space;
    }

    public function storeRecords($deviceId, $records, $removeDuplicates = false) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        $user = $this->userModel->getUserById($device['user_id']);

        $lastDate = null;
        $fp = null;
        $filename = null;
        foreach ($records as $r) {
            $date = date('Y-m-d', $r['timestamp']);
            if ($date !== $lastDate) {
                if ($fp !== null) {
                    $this->close($fp, $filename, $removeDuplicates);
                }
                $filename = $this->getFileName($user, $device, $r['timestamp']);
                $fp = $this->open($filename);
            }
            $this->writeRecord($fp, $r);
            $lastDate = $date;
        }
        if ($fp !== null) {
            $this->close($fp, $filename, $removeDuplicates);
        }
    }

    private function writeHeader($fp) {
        fwrite($fp, implode(';', CsvModel::FIELDS)."\n");
    }

    private function writeRecord($fp, $record) {
        $row = array();
        foreach (CsvModel::FIELDS as $f) {
            $row[] = $record[$f];
        }
        fwrite($fp, implode(';', $row)."\n");
    }

    private function getFileName($user, $device, $ts) {
        $dir = sprintf("%s/%s-%s/%s", $user['domain'], $device['name'], $device['esp8266_id'], date('Y-m', $ts));
        return "$dir/".date('Y-m-d', $ts).".csv";
    }

    private function open($filename) {
        $fileExists = $this->space->DoesObjectExist($filename);
        $tmpName = tempnam(sys_get_temp_dir(), str_replace('/', '_', $filename));
        if ($fileExists) {
            $this->space->DownloadFile($filename, $tmpName);
            $fp = fopen($tmpName, 'a');
        } else {
            $fp = fopen($tmpName, 'a');
            $this->writeHeader($fp);
        }
        return $fp;
    }

    private function close($fp, $filename, $removeDuplicates) {
        $metadata = stream_get_meta_data($fp);
        $tmpFileName = $metadata['uri'];
        fclose($fp);
        if ($removeDuplicates) {
            $this->removeDuplicates($tmpFileName);
        }
        $this->space->UploadFile($tmpFileName, 'private', $filename);
        unlink($tmpFileName);
    }

    private function removeDuplicates($filename) {
        $records = array();

        if (!file_exists($filename)) {
            return;
        }

        $fp = fopen($filename, 'r');
        fgets($fp); // header
        while(!feof($fp))  {
            $line = trim(fgets($fp));
            $r = explode(';', $line);
            $records[$r[0]] = $line;
        }
        fclose($fp);

        ksort($records, SORT_NUMERIC);

        $fp = fopen($filename, 'w');
        $this->writeHeader($fp);
        foreach ($records as $r) {
            fwrite($fp, $r."\n");
        }
        fclose($fp);
    }
}
?>
