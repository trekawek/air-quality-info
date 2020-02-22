<?php
namespace AirQualityInfo\Lib;
 
class SensorCommunityApi {

    const URL = 'https://data.sensor.community/static/v1/data.json';

    private $data = null;

    public function getRecords($sensorIds) {
        $result = array();
        SensorCommunityApi::read(function (array $item) use (&$result, &$sensorIds) {
            if (in_array($item['sensor']['id'], $sensorIds)) {
                $result[] = $item;
            }
        });
        return $result;
    }

    public function getMatchingSensors($sensorId) {
        $locationToSensors = array();
        $location = null;
        SensorCommunityApi::read(function (array $item) use (&$locationToSensors, &$location, $sensorId) {
            $sId = $item['sensor']['id'];
            $lId = $item['location']['id'];
            if (!isset($locationToSensors[$lId])) {
                $locationToSensors[$lId] = array();
            }
            $locationToSensors[$lId][] = $sId;
            if ($sId == $sensorId) {
                $location = $item['location'];
            }
        });
        if ($location !== null) {
            return array(array_unique($locationToSensors[$location['id']]), $location);
        } else {
            return array(array(), array());
        }
    }

    private static function read($listener) {
        $parser = new \JsonCollectionParser\Parser();
        $stream = fopen(SensorCommunityApi::URL, 'r');
        $parser->parse($stream, $listener);
    }
}

?>