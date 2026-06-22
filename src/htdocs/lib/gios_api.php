<?php
namespace AirQualityInfo\Lib;
 
class GiosApi {

    const VALUE_MAPPING = array(
        'PM2.5' => 'pm25',
        'PM10'  => 'pm10',
    );

    const STATION_URL = 'https://api.gios.gov.pl/pjp-api/v1/rest/station/sensors/';

    const DATA_URL = 'https://api.gios.gov.pl/pjp-api/v1/rest/data/getData/';

    private $data = null;

    public function getRecord($sensorId) {
        $opts = array("ssl" => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ));
        $ctx = stream_context_create($opts);
        
        $parser = new \JsonCollectionParser\Parser();
        $endpointIds = array();

        $remoteStream = fopen(GiosApi::STATION_URL . $sensorId . '?size=500', 'r', false, $ctx);
        $parser->parse($remoteStream, function (array $endpoint) use (&$endpointIds) {
            foreach($endpoint["Lista stanowisk pomiarowych dla podanej stacji"] as $v) {
                $code = $v['Wskaźnik - kod'];
                if(isset(GiosApi::VALUE_MAPPING[$code])) {
                    $mappedCode = GiosApi::VALUE_MAPPING[$code];
                    if (isset($endpointIds[$mappedCode])) {
                        continue;
                    }
                    $endpointIds[GiosApi::VALUE_MAPPING[$code]] = $v['Identyfikator stanowiska'];
                }
            }
        });

        $record = array();
        foreach ($endpointIds as $key => $endpointId) {
            $remoteStream = fopen(GiosApi::DATA_URL . $endpointId, 'r', false, $ctx);
            $parser->parse($remoteStream, function ($data) use (&$record, $key) {
                foreach ($data['Lista danych pomiarowych'] as $v) {
                    if ($v['Wartość'] !== null) {
                        $parsed = \DateTime::createFromFormat('Y-m-d H:i:s', $v['Data'], new \DateTimeZone('Europe/Warsaw'));
                        if ($parsed !== false) {
                            $record['timestamp'] = $parsed->getTimestamp();
                        }
                        $record[$key] = $v['Wartość'];
                        break;
                    }
                }
            });
        }
        return $record;
    }
}

?>