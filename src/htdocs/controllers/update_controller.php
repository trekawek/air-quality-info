<?php
class UpdateController extends AbstractController {

    const VALUE_MAPPING = array(
        'pm1'         => array(          'PMS_P0'),
        'pm10'        => array('SDS_P1', 'PMS_P1', 'HPM_P1'),
        'pm25'        => array('SDS_P2', 'PMS_P2', 'HPM_P2'),
        'temperature' => array('BME280_temperature', 'BMP_temperature'),
        'humidity'    => 'BME280_humidity',
        'pressure'    => array('BME280_pressure', 'BMP_pressure'),
        'heater_temperature' => 'temperature',
        'heater_humidity'    => 'humidity',
        'gps_time'    => 'GPS_time',
        'gps_date'    => 'GPS_date',
    );

    private $dao;

    public function __construct($dao) {
        parent::__construct();
        $this->dao = $dao;
    }

    public function update($device) {
        $this->authenticate($device);

        $payload = file_get_contents("php://input");
        $data = json_decode($payload, true);
        $sensors = $data['sensordatavalues'];
        
        $map = array();
        foreach ($sensors as $row) {
            $map[$row['value_type']] = $row['value'];
        }
        
        if ($device['esp8266id'] != $data['esp8266id']) {
            error_log('esp8266id mismatch. Expected: '.$device['esp8266id'].' but got '.$data['esp8266id']);
            exit;
        }
        
        $time = time();
        
        $gps_date = UpdateController::readValue($device, 'gps_date', $map, null);
        $gps_time = UpdateController::readValue($device, 'gps_time', $map, null);
        if ($gps_date && $gps_time) {
            $time = DateTime::createFromFormat('m/d/Y H:i:s.u', $gps_date.' '.$gps_time, new DateTimeZone('UTC'))->getTimestamp();
        }
        
        if (CONFIG['store_json_payload']) {
            $this->dao->logJsonUpdate($device['esp8266id'], $time, $payload);
        }
        
        $pressure = UpdateController::readValue($device, 'pressure', $map);
        if ($pressure !== null) {
            $pressure /= 100;
        }
        
        echo $this->dao->update(
            $device['esp8266id'],
            $time,
            UpdateController::readValue($device, 'pm25', $map),
            UpdateController::readValue($device,  'pm10', $map),
            UpdateController::readValue($device, 'temperature', $map),
            $pressure,
            UpdateController::readValue($device, 'humidity', $map),
            UpdateController::readValue($device, 'heater_temperature', $map),
            UpdateController::readValue($device, 'heater_humidity', $map)
        );
    }

    private static function readValue($device, $valueName, $sensorValues, $undefinedValue = null) {
        $value = null;
        if (!isset(UpdateController::VALUE_MAPPING[$valueName])) {
            return $undefinedValue;
        }
        $mappedNames = UpdateController::VALUE_MAPPING[$valueName];
        if (!is_array($mappedNames)) {
            $mappedNames = array($mappedNames);
        }
        foreach ($mappedNames as $mappedName) {
            if (isset($sensorValues[$mappedName])) {
                $value = $sensorValues[$mappedName];
                break;
            }
        }
        return $value == null ? $undefinedValue : $value;
    }
}

?>