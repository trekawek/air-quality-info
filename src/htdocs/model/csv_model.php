<?php
namespace AirQualityInfo\Model;

class CsvModel {

    const FIELDS = array('timestamp', 'pm25','pm10','temperature','pressure','humidity','heater_temperature','heater_humidity');

    private $deviceModel;

    private $userModel;

    private $s3Bucket;

    private $s3Client;

    public function __construct(DeviceModel $deviceModel, UserModel $userModel, \Aws\S3\S3Client $s3Client, $s3Bucket) {
        $this->deviceModel = $deviceModel;
        $this->userModel = $userModel;
        $this->s3Client = $s3Client;
        $this->s3Bucket = $s3Bucket;
    }

    public function listDirs($dirPath) {
        $dirPath = explode('/', $dirPath);
        $prefix = '';
        foreach($dirPath as $segment) {
            if (!empty($segment)) {
                $prefix .= $segment.'/';
            }
        }
        $objects = $this->s3Client->ListObjects(array(
            'Bucket' => $this->s3Bucket,
            'Prefix' => $prefix,
            'Delimiter' => '/'
        ));
        return array_map(function($el) {
            return substr($el['Prefix'], 0, -1);
        }, $objects->get('CommonPrefixes'));
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
        $fileExists = $this->s3Client->doesObjectExist($this->s3Bucket, $filename);
        $tmpName = tempnam(sys_get_temp_dir(), str_replace('/', '_', $filename));
        if ($fileExists) {
            $this->s3Client->getObject(array(
                'Bucket' => $this->s3Bucket,
                'Key'    => $filename,
                'SaveAs' => $tmpName
            ));
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
        $this->s3Client->putObject(array(
            'Bucket'      => $this->s3Bucket,
            'Key'         => $filename,
            'SourceFile'  => $tmpFileName
        ));
        $this->s3Client->waitUntil('ObjectExists', array(
            'Bucket' => $this->s3Bucket,
            'Key'    => $filename
        ));        
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
