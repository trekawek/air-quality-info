<?php
namespace AirQualityInfo\Model;

class Updater {

    const VALUE_MAPPING = array(
        'pm10'        => array('SDS_P1', 'PMS_P1', 'HPM_P1', 'SPS30_P1', 'temperature_1'),
        'pm25'        => array('SDS_P2', 'PMS_P2', 'HPM_P2', 'SPS30_P2', 'temperature_2'),
        'pm1'         => array(          'PMS_P0',           'SPS30_P0'),
        'pm4'         => array(                              'SPS30_P4'),
        'n05'         => array(                              'SPS30_N05'),
        'n1'          => array(                              'SPS30_N1'),
        'n25'         => array(                              'SPS30_N25'),
        'n4'          => array(                              'SPS30_N4'),
        'n10'         => array(                              'SPS30_N10'),
        'co2'         => array('conc_co2_ppm'),
        'temperature' => array('SHT3X_temperature', 'BME280_temperature', 'BMP_temperature', 'BMP280_temperature', 'HTU21_temperature', 'DHT22_temperature', 'SHT1x_temperature', 'temperature_3'),
        'humidity'    => array('SHT3X_humidity', 'BME280_humidity', 'HTU21_humidity', 'DHT22_humidity', 'SHT1x_humidity', 'relative_humidity_1'),
        'pressure'    => array('BME280_pressure', 'BMP_pressure', 'BMP280_pressure'),
        'ambient_light'      => array('ambient_light'),
        'wind_speed'  => array('wind_speed'),  // km/h
        'rainfall'    => array('rainfall'),    // mm
        'noise_level' => array('DNMS_noise_LAeq'), // db
        'heater_temperature' => array('temperature', 'HECA_temperature'),
        'heater_humidity'    => array('humidity', 'HECA_humidity'),
        'gps_time'    => array('GPS_time'),
        'gps_date'    => array('GPS_date'),
        'gps_lat'    => array('GPS_lat'),
        'gps_lon'    => array('GPS_lon'),
        'gps_height'    => array('GPS_height'),
    );

    const PREDEFINED_ADJUSTMENTS = array(
        'OpenAirSen' => array(
            'SDS_P1'   => array('multiplier' => 1.090, 'offset' =>  23.70),
            'SDS_P2'   => array('multiplier' => 1.370, 'offset' =>  5.600),
            'PMS_P1'   => array('multiplier' => 1.088, 'offset' => -0.761),
            'PMS_P2'   => array('multiplier' => 1.079, 'offset' => -0.324),
            'SPS30_P1' => array('multiplier' => 0.699, 'offset' =>  2.377),
            'SPS30_P2' => array('multiplier' => 0.833, 'offset' =>  3.416),
        ),
    );

    private $record_model;

    private $device_model;

    public function __construct(RecordModel $record_model, DeviceModel $device_model) {
        $this->record_model = $record_model;
        $this->device_model = $device_model;
    }

    public function update($device, $time, $map) {
        $mapping = $this->getMapping($device);
        $gps_date = Updater::readValue($mapping, $device, 'gps_date', $map, null);
        $gps_time = Updater::readValue($mapping, $device, 'gps_time', $map, null);
        if ($gps_date && $gps_time && $gps_date != '00/00/2000') {
            $parsedTime = \DateTime::createFromFormat('m/d/Y H:i:s.u', $gps_date.' '.$gps_time, new \DateTimeZone('UTC'))->getTimestamp();
            if ($parsedTime <= ($time + 60 * 60 * 24)) {
                $time = $parsedTime;
            }
        }

        $gps_lat = Updater::readValue($mapping, $device, 'gps_lat', $map, null);
        $gps_lon = Updater::readValue($mapping, $device, 'gps_lon', $map, null);
        $gps_height = Updater::readValue($mapping, $device, 'gps_height', $map, null);
        
        if ($gps_lat !== null && $gps_lon !== null && $gps_lat > -200 && $gps_lon > -200) {
            if ($gps_lat != $device['lat'] || $gps_lon != $device['lon']) {
                $this->device_model->updateDevice($device['id'], array(
                    'lat' => $gps_lat,
                    'lng' => $gps_lon,
                ));
            }
        }

        if ($gps_height !== null && $gps_height > -1000) {
            if ($gps_height != $device['elevation']) {
                $this->device_model->updateDevice($device['id'], array(
                    'elevation' => $gps_height,
                ));
            }
        }
        
        $this->insert($device, $time, $map);
    }

    public function insert($device, $time, $data) {
        return $this->insertBatch($device, array(array('time' => $time, 'data' => $data)));
    }

    public function insertBatch($device, $batch) {
        $mapping = $this->getMapping($device);
        $adjustments = $this->device_model->getDeviceAdjustments($device['id']);

        $records = array();
        foreach ($batch as $row) {
            $data = $row['data'];
            if ($device['predefined_adjustment'] != null) {
                $data = Updater::applyPredefinedAdjustment($device['predefined_adjustment'], $data);
            }
            $r = array(
                'timestamp'   => $row['time'],
            );
            foreach (array_keys(Updater::VALUE_MAPPING) as $k) {
                $r[$k] = Updater::readValue($mapping, $device, $k, $data);
            }
            foreach ($adjustments as $a) {
                $f = $a['db_name'];
                if (isset($r[$f]) && $r[$f] !== null) {
                    $r[$f] = $r[$f] * $a['multiplier'] + $a['offset'];
                }
            }
            if (!isset($r['pm10']) || $r['pm10'] == null) {
                continue;
            }
            $records[] = $r;
        }
        $this->record_model->update($device['id'], $records);
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
        return $value === null ? $undefinedValue : $value;
    }

    private static function applyPredefinedAdjustment($adjustmentType, $data) {
        if (!isset(Updater::PREDEFINED_ADJUSTMENTS[$adjustmentType])) {
            return $data;
        }
        $adjustments = Updater::PREDEFINED_ADJUSTMENTS[$adjustmentType];
        foreach ($adjustments as $f => $a) {
            if (isset($data[$f])) {
                $data[$f] = $data[$f] * $a['multiplier'] + $a['offset'];
            }
        }
        return $data;
    }

    private function getMapping($device) {
        $mapping = Updater::VALUE_MAPPING;
        if (isset($device['mapping'])) {
            foreach ($device['mapping'] as $dbType => $jsonTypes) {
                foreach ($mapping as $mDbType => $mJsonTypes) {
                    $mapping[$mDbType] = array_diff($mJsonTypes, $jsonTypes);
                }
            }
            $mapping = array_merge($mapping, $device['mapping']);
        }
        return $mapping;
    }
}

?>