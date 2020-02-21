<?php
namespace AirQualityInfo\Lib;
 
class SensorCommunityApi {

    const URL = 'https://data.sensor.community/static/v1/data.json';

    private $data = null;

    public function getRecords($sensorIds) {
        $result = array();
        foreach ($this->getData(true) as $row) {
            if (in_array($row['sensor']['id'], $sensorIds)) {
                $result[] = $row;
            }
        }
        return $result;
    }

    public function getMatchingSensors($sensorId) {
        $locationId = null;
        foreach ($this->getData() as $row) {
            if ($sensorId == $row['sensor']['id']) {
                $locationId = $row['location']['id'];
            }
        }
        if ($locationId === null) {
            return array($sensorId);
        }

        $sensorIds = array();
        foreach ($this->getData() as $row) {
            if ($locationId == $row['location']['id']) {
                $sensorIds[] = $row['sensor']['id'];
            }
        }
        return array_unique($sensorIds);
    }

    private function getData($forceReload = false) {
        if ($this->data === null || $forceReload) {
            $this->data = SensorCommunityApi::read();
        }
        return $this->data;
    }

    private static function read() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, SensorCommunityApi::URL);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}

?>