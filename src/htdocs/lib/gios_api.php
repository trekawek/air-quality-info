<?php
namespace AirQualityInfo\Lib;
 
class GiosApi {

    const VALUE_MAPPING = array(
        'PM2.5' => 'pm25',
        'PM10'  => 'pm10',
    );

    const STATION_URL = 'https://api.gios.gov.pl/pjp-api/rest/station/sensors/';

    const DATA_URL = 'http://api.gios.gov.pl/pjp-api/rest/data/getData/';

    private $data = null;

    public function getRecord($sensorId) {
        $opts = array("ssl" => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ));
        $ctx = stream_context_create($opts);
        
        $parser = new \JsonCollectionParser\Parser();
        $endpointIds = array();

        $remoteStream = fopen(GiosApi::STATION_URL . $sensorId, 'r', false, $ctx);
        $parser->parse($remoteStream, function (array $endpoint) use (&$endpointIds) {
            $paramCode = $endpoint['param']['paramCode'];
            if (isset(GiosApi::VALUE_MAPPING[$paramCode])) {
                $endpointIds[GiosApi::VALUE_MAPPING[$paramCode]] = $endpoint['id'];
            }
        });
        $record = array();
        foreach ($endpointIds as $key => $endpointId) {
            $remoteStream = fopen(GiosApi::DATA_URL . $endpointId, 'r', false, $ctx);
            $parser->parse($remoteStream, function ($data) use (&$record, $key) {
                foreach ($data['values'] as $v) {
                    if ($v['value'] !== null) {
                        $record['timestamp'] = \DateTime::createFromFormat('Y-m-d H:i:s', $v['date'], new \DateTimeZone('Europe/Warsaw'))->getTimestamp();
                        $record[$key] = $v['value'];
                        break;
                    }
                }
            });
        }
        if (isset($record['timestamp'])) {
            if (time() - $record['timestamp'] < 2 * 60 * 60) {
                $record['timestamp'] = time();
            }
        }
        return $record;
    }
}

?>