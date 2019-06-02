<?php
namespace AirQualityInfo\Model;

class CsvModel {

    const FIELDS = array('timestamp', 'pm25','pm10','temperature','pressure','humidity','heater_temperature','heater_humidity');

    private $csvRoot;

    private $deviceModel;

    private $userModel;

    public function __construct(DeviceModel $deviceModel, UserModel $userModel) {
        $this->deviceModel = $deviceModel;
        $this->userModel = $userModel;
        if (isset(CONFIG['csv_root'])) {
            $this->csvRoot = CONFIG['csv_root'];
        } else {
            $this->csvRoot = null;
        }
    }

    public function storeRecords($deviceId, $records) {
        if ($this->csvRoot === null) {
            return;
        }

        $device = $this->deviceModel->getDeviceById($deviceId);
        $user = $this->userModel->getUserById($device['user_id']);

        $lastDate = null;
        $fp = null;
        foreach ($records as $r) {
            $date = date('Y-m-d', $r['timestamp']);
            if ($date !== $lastDate) {
                if ($fp !== null) {
                    fclose($fp);
                }
                $filename = $this->getFileName($user, $device, $r['timestamp']);
                $newFile = !file_exists($filename);
                $fp = fopen($filename, 'a');
                if ($newFile) {
                    $this->writeHeader($fp);
                }
            }
            $this->writeRecord($fp, $r);
            $lastDate = $date;
        }
        if ($fp !== null) {
            fclose($fp);
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
        $dir = sprintf("%s/%s/%s-%s/%s", $this->csvRoot, $user['domain'], $device['name'], $device['esp8266_id'], date('Y-m', $ts));
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return "$dir/".date('Y-m-d', $ts).".csv";
    }
}
?>