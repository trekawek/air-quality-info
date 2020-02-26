<?php
namespace AirQualityInfo\Lib;
 
class SensorCommunityApi {

    const URL = 'https://data.sensor.community/static/v1/data.json';

    const CACHE_FILE = "/var/aqi/sensor-community-data.json";

    private $data = null;

    public function getRecords($sensorIds) {
        $result = array();
        SensorCommunityApi::read(function (array $item) use (&$result, &$sensorIds) {
            if (in_array($item['sensor']['id'], $sensorIds)) {
                $result[] = $item;
            }
        }, true);
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

    private static function read($listener, $forceReload = false) {
        if (!file_exists(SensorCommunityApi::CACHE_FILE) || $forceReload) {
            $opts = array("ssl" => array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ));
            $ctx = stream_context_create($opts);
            $remoteStream = fopen(SensorCommunityApi::URL, 'r', false, $ctx);
            $localFile = tempnam('/tmp', 'sensor-community-data.json');
            $localStream = fopen($localFile, 'w');
            stream_copy_to_stream($remoteStream, $localStream);
            fclose($localStream);
            fclose($remoteStream);
            rename($localFile, SensorCommunityApi::CACHE_FILE);
        }
        $stream = fopen(SensorCommunityApi::CACHE_FILE, 'r');
        $parser = new \JsonCollectionParser\Parser();
        $parser->parse($stream, $listener);
    }
}

?>