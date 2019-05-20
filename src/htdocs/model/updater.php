<?php
namespace AirQualityInfo\Model;

class Updater {

    const VALUE_MAPPING = array(
        'pm1'         => array(          'PMS_P0'),
        'pm10'        => array('SDS_P1', 'PMS_P1', 'HPM_P1'),
        'pm25'        => array('SDS_P2', 'PMS_P2', 'HPM_P2'),
        'temperature' => array('BME280_temperature', 'BMP_temperature'),
        'humidity'    => 'BME280_humidity',
        'pressure'    => array('BME280_pressure', 'BMP_pressure'),
        'heater_temperature' => array('temperature', 'HECA_temperature'),
        'heater_humidity'    => array('humidity', 'HECA_humidity'),
        'gps_time'    => 'GPS_time',
        'gps_date'    => 'GPS_date',
    );

    private $record_model;

    public function __construct(RecordModel $record_model) {
        $this->record_model = $record_model;
    }

    public function update($device, $map) {
        $time = time();

        $mapping = $this->getMapping($device);
        $gps_date = Updater::readValue($mapping, $device, 'gps_date', $map, null);
        $gps_time = Updater::readValue($mapping, $device, 'gps_time', $map, null);
        if ($gps_date && $gps_time) {
            $time = DateTime::createFromFormat('m/d/Y H:i:s.u', $gps_date.' '.$gps_time, new DateTimeZone('UTC'))->getTimestamp();
        }
        
        $this->insert($device, $time, $map);
    }

    public function insert($device, $time, $map) {
        $mapping = $this->getMapping($device);

        $pressure = Updater::readValue($mapping, $device, 'pressure', $map);
        if ($pressure !== null) {
            $pressure /= 100;
        }
        
        echo $this->record_model->update(
            $device['id'],
            $time,
            Updater::readValue($mapping, $device, 'pm25', $map),
            Updater::readValue($mapping, $device, 'pm10', $map),
            Updater::readValue($mapping, $device, 'temperature', $map),
            $pressure,
            Updater::readValue($mapping, $device, 'humidity', $map),
            Updater::readValue($mapping, $device, 'heater_temperature', $map),
            Updater::readValue($mapping, $device, 'heater_humidity', $map)
        );
    }

    private static function readValue($mapping, $device, $valueName, $sensorValues, $undefinedValue = null) {
        $value = null;
        if (!isset($mapping[$valueName])) {
            return $undefinedValue;
        }
        $mappedNames = $mapping[$valueName];
        if ($mappedNames === null) {
            $mappedNames = array();
        }
        if (!is_array($mappedNames)) {
            $mappedNames = array($mappedNames);
        }
        foreach ($mappedNames as $mappedName) {
            if (isset($sensorValues[$mappedName]) && $sensorValues[$mappedName] !== null) {
                $value = $sensorValues[$mappedName];
                break;
            }
        }
        return $value == null ? $undefinedValue : $value;
    }

    private function getMapping($device) {
        $mapping = Updater::VALUE_MAPPING;
        if (isset($device['mapping'])) {
            $mapping = array_merge($mapping, $device['mapping']);
        }
        return $mapping;
    }
}

?>