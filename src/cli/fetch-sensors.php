#!/usr/bin/env php
<?php
ini_set('memory_limit', '402653184');

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

    private $deviceModel;

    private $recordModel;

    public function __construct(
        \AirQualityInfo\Lib\SensorCommunityApi $sensorsApi,
        \AirQualityInfo\Model\DeviceModel $deviceModel,
        \AirQualityInfo\Model\RecordModel $recordModel) {
        $this->sensorsApi = $sensorsApi;
        $this->deviceModel = $deviceModel;
        $this->recordModel = $recordModel;
    }

    function run() {
        list($locations, $records) = $this->readSensorData();
        echo 'Inserting '.count($records)." records\n";
        $this->insertRecords($records);
        $this->updateDevices($locations);
    }

    function updateDevices($locations) {
        foreach ($locations as $deviceId => $values) {
            $device = $this->deviceModel->getDeviceById($deviceId);
            $data = array();
            foreach (array('lat', 'lng', 'elevation') as $k) {
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
            $this->recordModel->update($deviceId, array($record));
        }
    }

    private function readSensorData() {
        $sensors = $this->deviceModel->getSensors();
        $sensorIds = array();
        $sensorToDevices = array();
        $deviceData = array();
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
}

$task = $diContainer->injectClass('FetchSensorTask');

while (true) {
    echo "Fetching sensor data\n";
    $task->run();
    sleep(60);
}

?>