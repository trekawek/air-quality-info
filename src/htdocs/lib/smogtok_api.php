<?php
namespace AirQualityInfo\Lib;
 
class SmogtokApi {

    const VALUE_MAPPING = array(
        'Temperatura' => 'temperature',
        'PM 2,5' => 'pm25',
        'PM 10' => 'pm10',
        'Ciśnienie' => 'pressure',
        'Wilgotność' => 'humidity'
    );

    const URL = 'https://smogtok.com/apprest/probedata/';

    private $data = null;

    public function getRecord($sensorId) {
        $opts = array("ssl" => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ));
        $ctx = stream_context_create($opts);
        $remoteStream = fopen(SmogtokApi::URL . $sensorId, 'r', false, $ctx);
        $parser = new \JsonCollectionParser\Parser();

        $record = array();
        $parser->parse($remoteStream, function (array $item) use (&$record) {
            foreach ($item['REGS'] as $r) {
                $name = $r['REGNAME'];
                $value = $r['VALUE'];
                $record[SmogtokApi::VALUE_MAPPING[$name]] = $value;
            }
            if (isset($record['pressure'])) {
                $record['pressure'] *= 100;
            }
            $record['timestamp'] = \DateTime::createFromFormat('Y-m-d H:i:s', $item['DT'], new \DateTimeZone('Europe/Warsaw'))->getTimestamp();
        });

        return $record;
    }
}

?>