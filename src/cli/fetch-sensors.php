#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

class FetchSensorTask {

    const locationMapping = array(
        'latitude' => 'lat',
        'longitude' => 'lng',
        'altitude' => 'elevation',
    );

    const valueMapping = array(
        'P1' => 'pm10',
        'P2' => 'pm25',
        'temperature' => 'temperature',
        'humidity' => 'humidity',
        'pressure' => 'pressure',
    );

    private $sensorsApi;

    private $smogtokApi;

    private $syngeosApi;

    private $giosApi;

    private $deviceModel;

    private $recordModel;

    public function __construct(
        \AirQualityInfo\Lib\SensorCommunityApi $sensorsApi,
        \AirQualityInfo\Lib\SmogtokApi $smogtokApi,
        \AirQualityInfo\Lib\SyngeosApi $syngeosApi,
        \AirQualityInfo\Lib\GiosApi $giosApi,
        \AirQualityInfo\Model\DeviceModel $deviceModel,
        \AirQualityInfo\Model\RecordModel $recordModel) {
        $this->sensorsApi = $sensorsApi;
        $this->smogtokApi = $smogtokApi;
        $this->syngeosApi = $syngeosApi;
        $this->giosApi = $giosApi;
        $this->deviceModel = $deviceModel;
        $this->recordModel = $recordModel;
    }

    function run() {
        echo "Reading sensor.community data\n";
        list($locations, $records) = $this->readSensorCommunityData();
        echo 'Inserting '.count($records)." records\n";
        $this->insertRecords($records);
        $this->updateDevices($locations);

        echo "Reading smogtok.com data\n";
        $records = $this->readSmogtokData();
        echo 'Inserting '.count($records)." records\n";
        $this->insertRecords($records);

        echo "Reading syngeos data\n";
        $records = $this->readSyngeosData();
        echo 'Inserting '.count($records)." records\n";
        $this->insertRecords($records);

        echo "Reading GIOS data\n";
        $records = $this->readGiosData();
        echo 'Inserting '.count($records)." records\n";
        $this->insertRecords($records);
    }

    function updateDevices($locations) {
        foreach ($locations as $deviceId => $values) {
            $device = $this->deviceModel->getDeviceById($deviceId);
            $data = array();
            foreach (array('lat', 'lng', 'elevation') as $k) {
                if (!isset($values[$k])) {
                    continue;
                }
                $v1 = floatval($device[$k]);
                $v2 = floatval($values[$k]);
                if (abs($v1 - $v2) > 0.01) {
                    $data[$k] = $values[$k];
                }
            }
            if (!empty($data)) {
                $this->deviceModel->updateDevice($deviceId, $data);
            }
        }
    }

    function insertRecords($records) {
        foreach ($records as $deviceId => $record) {
            if (empty($record)) {
                continue;
            }
            $this->recordModel->update($deviceId, array($record));
        }
    }

    private function readSensorCommunityData() {
        $sensors = $this->deviceModel->getSensors('sensor.community');
        $sensorIds = array();
        $sensorToDevices = array();
        $deviceData = array();
        $locations = array();
        $records = array();
        if (empty($sensors)) {
            return array($locations, $records);
        }
        foreach ($sensors as $r) {
            $sensorId = $r['sensor_id'];
            $sensorIds[] = $r['sensor_id'];
            if (!isset($sensorToDevices[$sensorId])) {
                $sensorToDevices[$sensorId] = array();
            }
            $sensorToDevices[$sensorId][] = $r['device_id'];
            $locations[$r['device_id']] = array();
            $records[$r['device_id']] = array();
        }

        foreach ($this->sensorsApi->getRecords($sensorIds) as $r) {
            foreach ($sensorToDevices[$r['sensor']['id']] as $deviceId) {
                foreach ($r['location'] as $k => $v) {
                    if (isset(FetchSensorTask::locationMapping[$k])) {
                        $locations[$deviceId][FetchSensorTask::locationMapping[$k]] = $v;
                    }
                }
                foreach ($r['sensordatavalues'] as $v) {
                    $k = $v['value_type'];
                    $v = $v['value'];
                    if (isset(FetchSensorTask::valueMapping[$k])) {
                        $records[$deviceId][FetchSensorTask::valueMapping[$k]] = $v;
                    }
                }
                $records[$deviceId]['timestamp'] = DateTime::createFromFormat('Y-m-d H:i:s', $r['timestamp'], new DateTimeZone('UTC'))->getTimestamp();
            }
        }

        return array($locations, $records);
    }

    private function readSmogtokData() {
        $sensors = $this->deviceModel->getSensors('smogtok');
        $records = array();
        if (empty($sensors)) {
            return $records;
        }
        foreach ($sensors as $s) {
            $r = $this->smogtokApi->getRecord($s['sensor_id']);
            $records[$s['device_id']] = $r;
        }
        return $records;
    }

    private function readSyngeosData() {
        $sensors = $this->deviceModel->getSensors('syngeos');
        $records = array();
        if (empty($sensors)) {
            return $records;
        }
        foreach ($sensors as $s) {
            $r = $this->syngeosApi->getRecord($s['sensor_id']);
            $records[$s['device_id']] = $r;
        }
        return $records;
    }

    private function readGiosData() {
        $sensors = $this->deviceModel->getSensors('gios');
        $records = array();
        if (empty($sensors)) {
            return $records;
        }
        foreach ($sensors as $s) {
            $r = $this->giosApi->getRecord($s['sensor_id']);
            $records[$s['device_id']] = $r;
        }
        return $records;
    }
}

$task = $diContainer->injectClass('FetchSensorTask');

while (true) {
    echo "Fetching sensor data\n";
    $task->run();
    sleep(60);
}

?>