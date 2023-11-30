<?php
namespace AirQualityInfo\Model;

use \AirQualityInfo\Lib\StringUtils;

class CsvModel {

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

    public function list($dirPath) {
        $objects = $this->s3Client->ListObjects(array(
            'Bucket' => $this->s3Bucket,
            'Prefix' => $dirPath.'/',
            'Delimiter' => '/'
        ));
        if ($objects->get('CommonPrefixes') === null) {
            $result['dirs'] = array();
        } else {
            $result['dirs'] = array_map(function($el) {
                return substr($el['Prefix'], 0, -1);
            }, $objects->get('CommonPrefixes'));
        }
        if ($objects->get('Contents') === null) {
            $result['objects'] = array();
        } else {
            $result['objects'] = $objects->get('Contents');
        }
        $result['objects'] = array_filter($result['objects'], function($el) {
            return substr($el['Key'], -1) !== '/';
        });
        return $result;
    }

    public function downloadFile($filePath) {
        if ($this->s3Client->doesObjectExist($this->s3Bucket, $filePath)) {
            $tmpName = tempnam(sys_get_temp_dir(), str_replace('/', '_', $filePath));
            $object = $this->s3Client->getObject(array(
                'Bucket' => $this->s3Bucket,
                'Key'    => $filePath,
                'SaveAs' => $tmpName
            ));
            $this->translateTimeStamps($tmpName);
            header('Content-Type: '.$object->get('ContentType'));
            header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
            header('Content-Length: '.filesize($tmpName));
            readfile($tmpName);
            unlink($tmpName);
            return true;
        } else {
            return false;
        }
    }

    public function downloadDir($dirPath) {
        $objects = $this->s3Client->ListObjects(array(
            'Bucket' => $this->s3Bucket,
            'Prefix' => $dirPath.'/'
        ));
        $toCleanUp = array();

        $zip = new \ZipArchive();
        $zipName = tempnam(sys_get_temp_dir(), str_replace('/', '_', $dirPath));
        $toCleanUp[] = $zipName;
        if ($zip->open($zipName, \ZipArchive::CREATE) === TRUE) {
            foreach ($objects->get('Contents') as $obj) {
                $tmpName = tempnam(sys_get_temp_dir(), str_replace('/', '_', $obj['Key']));
                $object = $this->s3Client->getObject(array(
                    'Bucket' => $this->s3Bucket,
                    'Key'    => $obj['Key'],
                    'SaveAs' => $tmpName
                ));
                $this->translateTimeStamps($tmpName);
                $zip->addFile($tmpName, basename($dirPath).'/'.StringUtils::removePrefix($obj['Key'], $dirPath.'/'));
                $toCleanUp[] = $tmpName;
            }
            $zip->close();
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="'.basename($dirPath).'.zip"');
            header('Content-Length: '.filesize($zipName));
            readfile($zipName);
            foreach ($toCleanUp as $f) {
                unlink($f);
            }
            return true;
        } else {
            return false;
        }
    }

    private function translateTimeStamps($csvFile) {
        if (!file_exists($csvFile)) {
            return;
        }

        $fp = fopen($csvFile, 'r');
        $header = fgets($fp);
        $records = array();
        while(!feof($fp))  {
            $line = trim(fgets($fp));
            if (empty($line)) {
                continue;
            }
            $r = explode(';', $line);
            $r[0] = date('Y-m-d H:i:s', intval($r[0]));
            $records[] = implode(';', $r);
        }
        fclose($fp);

        $fp = fopen($csvFile, 'w');
        fwrite($fp, $header);
        foreach ($records as $r) {
            fwrite($fp, $r."\n");
        }
        fclose($fp);
    }

    public function storeRecords($deviceId, $records) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        $user = $this->userModel->getUserById($device['user_id']);
        $fields = explode(',', $device['csv_fields']);
        array_unshift($fields, 'timestamp');

        $lastDate = null;
        $fp = null;
        $filename = null;
        foreach ($records as $r) {
            $date = date('Y-m-d', $r['timestamp']);
            if ($date !== $lastDate) {
                if ($fp !== null) {
                    fclose($fp);
                    $fp = null;
                }
                $filename = $this->getFileName($user, $device, $r['timestamp']);
                $fp = $this->open($filename, $fields);
            }
            $this->writeRecord($fp, $r, $fields);
            $lastDate = $date;
        }
        if ($fp !== null) {
            fclose($fp);
            $fp = null;
        }
    }

    private function writeHeader($fp, $fields) {
        fwrite($fp, implode(';', $fields)."\n");
    }

    private function writeRecord($fp, $record, $fields) {
        $row = array();
        foreach ($fields as $f) {
            if (isset($record[$f])) {
                $row[] = $record[$f];
            } else {
                $row[] = '';
            }
        }
        fwrite($fp, implode(';', $row)."\n");
    }

    private function getFileName($user, $device, $ts) {
        if ($device['esp8266_id'] === null) {
            $dir = sprintf("%s/%s/%s", $user['domain'], $device['name'], date('Y-m', $ts));
        } else {
            $dir = sprintf("%s/%s-%s/%s", $user['domain'], $device['name'], $device['esp8266_id'], date('Y-m', $ts));
        }
        return "$dir/".date('Y-m-d', $ts).".csv";
    }

    private function open($filename, $fields) {
        $fullPath = CONFIG['csv_root'] . '/' . $filename;
        $parentDir = dirname($fullPath);
        
        if (!file_exists($parentDir)) {
            mkdir($parentDir, 0777, true);
        }

        if (file_exists($fullPath)) {
            $fp = fopen($fullPath, 'a');
        } else {
            $fp = fopen($fullPath, 'w');
            $this->writeHeader($fp, $fields);
        }
        return $fp;
    }
}
?>
