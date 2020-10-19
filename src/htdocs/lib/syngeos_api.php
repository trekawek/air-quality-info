<?php
namespace AirQualityInfo\Lib;
 
class SyngeosApi {

    const VALUE_MAPPING = array(
        'temperature' => 'temperature',
        'pm2_5' => 'pm25',
        'pm10' => 'pm10',
        'pm1' => 'pm1',
        'air_pressure' => 'pressure',
        'humidity' => 'humidity'
    );

    const URL = 'https://api.syngeos.pl/api/public/data/device/';

    private $data = null;

    public function getRecord($sensorId) {
        $opts = array("ssl" => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ));
        $ctx = stream_context_create($opts);
        $remoteStream = fopen(SyngeosApi::URL . $sensorId, 'r', false, $ctx);
        $parser = new \JsonCollectionParser\Parser();

        $record = array();
        $parser->parse($remoteStream, function (array $item) use (&$record) {
            foreach ($item['sensors'] as $r) {
                $name = $r['name'];
                $value = $r['data'][0]['value'];
                if (isset(SyngeosApi::VALUE_MAPPING[$name])) {
                    $record[SyngeosApi::VALUE_MAPPING[$name]] = $value;
                }
            }
            if (isset($record['pressure'])) {
                $record['pressure'] *= 100;
            }
            $record['timestamp'] = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $item['sensors'][0]['data'][0]['read_at'])->getTimestamp();
        });
        return $record;
    }
}

?>