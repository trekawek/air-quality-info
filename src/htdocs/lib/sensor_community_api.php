<?php
namespace AirQualityInfo\Lib;
 
class SensorCommunityApi {

    const ALL_SENSORS = 'https://maps.sensor.community/data/v2/data.json';

    public function getSensorValues($sensorId) {
        return $this->read("https://data.sensor.community/airrohr/v1/sensor/$sensorId/")[0];
    }

    public function getMatchingSensors($sensorId) {
        $data = $this->read(ALL_SENSORS);

        $locationId = null;
        foreach ($data as $row) {
            if ($sensorId == $row['sensor']['id']) {
                $locationId = $row['location']['id'];
            }
        }
        if ($locationId === null) {
            return array($sensorId);
        }

        $sensorIds = array();
        foreach ($data as $row) {
            if ($locationId == $row['location']['id']) {
                $sensorIds[] = $row['sensor']['id'];
            }
        }
        return $sensorIds;
    }

    private static function read($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}

?>