<?php
namespace AirQualityInfo\Model;

class RecordModel {

    const FIELDS = array('pm25','pm10','pm1','pm4','n05','n1','n25','n4','n10','co2','temperature','pressure','humidity','heater_temperature','heater_humidity');

    const AGGREGATES = array(
        array('resolution' => 1 * 180,      'ttl' => 24 * 60 * 60),
        array('resolution' => 7 * 180,      'ttl' => 7 * 24 * 60 * 60),
        array('resolution' => 30 * 180,     'ttl' => 30 * 24 * 60 * 60),
        array('resolution' => 365 * 180,    'ttl' => 365 * 24 * 60 * 60),
        array('resolution' => 24 * 60 * 60, 'ttl' => null)
    );

    private $pdo;

    private $csvModel;

    private $deviceModel;

    public function __construct($pdo, CsvModel $csvModel, DeviceModel $deviceModel) {
        $this->pdo = $pdo;
        $this->csvModel = $csvModel;
        $this->deviceModel = $deviceModel;
    }

    public function update($deviceId, $records) {
        if (empty($records)) {
            return;
        }
        usort($records, function($v1, $v2) {
            $t1 = $v1['timestamp'];
            $t2 = $v2['timestamp'];
            if ($t1 == $t2) {
                return 0;
            }
            return ($t1 < $t2) ? -1 : 1;
        });

        $sql = 'INSERT INTO `records` (`device_id`, `timestamp`, ';
        foreach (RecordModel::FIELDS as $f) {
            $sql .= "`$f`, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ') VALUES (?, ?, ';
        $sql .= str_repeat('?, ', count(RecordModel::FIELDS));
        $sql = substr($sql, 0, -2);
        $sql .= ')';

        $insertStmt = $this->pdo->prepare($sql);

        foreach ($records as $i => $record) {
            foreach ($record as $k => $v) {
                if (is_numeric($v) && is_nan(floatval($v))) {
                    $record[$k] = null;
                }
                if (in_array($k, array('temperature', 'heater_temperature')) && $v < -127) {
                    $record[$k] = null;
                }
                if (in_array($k, array('humidity', 'heater_humidity')) && $v < 0) {
                    $record[$k] = null;
                }
                if (in_array($k, array('pm25', 'pm10', 'pm1', 'pm4', 'n05', 'n1', 'n4', 'n10', 'co2', 'pressure')) && $v < 0) {
                    $record[$k] = null;
                }
            }
            if (isset($record['pressure']) && $record['pressure'] !== null) {
                $record['pressure'] /= 100;
                if ($record['pressure'] > 10000) { // fix the Smogomierz bug
                    $record['pressure'] /= 100;
                }
            }

            $param = array($deviceId, $record['timestamp']);
            foreach (RecordModel::FIELDS as $f) {
                if (isset($record[$f])) {
                    $param[] = $record[$f];
                } else {
                    $param[] = null;
                }
            }

            try {
                $insertStmt->execute($param);
                $insertStmt->closeCursor();
                $records[$i] = $record;
            } catch (\PDOException $exception) {
                // probably the record already exists
            }
        }

        $this->createAggregates($deviceId, $records[0]['timestamp'], $records[count($records) - 1]['timestamp']);
        $this->csvModel->storeRecords($deviceId, $records, count($records) > 1);
    }

    public function getLastData($deviceId) {
        $stmt = $this->pdo->prepare("SELECT * FROM `records` WHERE `device_id` = ? AND `pm10` IS NOT NULL ORDER BY `timestamp` DESC LIMIT 1");
        $stmt->execute([$deviceId]);
        $row = $stmt->fetch();
        $stmt->closeCursor();

        $data = array('last_update' => $row['timestamp']);
        foreach (RecordModel::FIELDS as $f) {
            $data[$f] = $row[$f];
        }
        $device = $this->deviceModel->getDeviceById($deviceId);
        $data['pressure'] = RecordModel::normalizePressure($data['pressure'], $device['elevation'], $data['temperature']);
        return $data;
    }

    public function getLastAvg($deviceId, $hours) {
        $fields = array();
        foreach (RecordModel::FIELDS as $f) {
            $fields[] = "AVG($f)";
        }
        $fields = implode(",", $fields);

        $stmt = $this->pdo->prepare("SELECT $fields FROM `records` WHERE `device_id` = ? AND `timestamp` >= ?");
        $since = time() - $hours * 60 * 60;
        $stmt->execute([$deviceId, $since]);

        $row = $stmt->fetch();
        $stmt->closeCursor();

        $data = array();
        foreach (RecordModel::FIELDS as $i => $f) {
            $data[$f] = $row[$i];
        }
        $device = $this->deviceModel->getDeviceById($deviceId);
        $data['pressure'] = RecordModel::normalizePressure($data['pressure'], $device['elevation'], $data['temperature']);
        return $data;
    }

    public function createAggregates($deviceId, $fromTs, $toTs) {
        $maxResolution = 0;
        $deleteStmt = $this->pdo->prepare("DELETE FROM `aggregates` WHERE `device_id` = ? AND `timestamp` < ? AND `resolution` = ?");
        foreach (RecordModel::AGGREGATES as $aggregate) {
            if ($aggregate['resolution'] > $maxResolution) {
                $maxResolution = $aggregate['resolution'];
            }
            $this->updateAggregate($deviceId, $aggregate['resolution'], $fromTs);
            if ($aggregate['ttl'] !== null) {
                $minTimestamp = $toTs - $aggregate['ttl'];
                $deleteStmt->execute([$deviceId, $minTimestamp, $aggregate['resolution']]);
            }
        }
        $deleteStmt->closeCursor();

        $stmt = $this->pdo->prepare("DELETE FROM `records` WHERE `device_id` = ? AND `timestamp` < ?");
        $minTimestamp = $toTs - $maxResolution;
        $stmt->execute([$deviceId, $minTimestamp]);
        $stmt->closeCursor();
    }

    private function updateAggregate($deviceId, $resolution, $fromTs) {
        $avgFields = array();
        $fields = array();
        foreach (RecordModel::FIELDS as $f) {
            $avgFields[] = "AVG(`$f`)";
            $fields[] = "`$f`";
        }
        $avgFields = implode(",", $avgFields);
        $fields = implode(",", $fields);
    
        $roundedTimestamp = ceil($fromTs / $resolution) * $resolution;
        $minRecordTimestamp = $roundedTimestamp - $resolution;

        $stmt = $this->pdo->prepare("DELETE FROM `aggregates` WHERE `device_id` = ? AND `timestamp` >= ? AND `resolution` = ?");
        $stmt->execute([$deviceId, $roundedTimestamp, $resolution]);
        $stmt->closeCursor();
        
        $sql = "INSERT INTO `aggregates` (`device_id`, `timestamp`, `resolution`, $fields)        
        SELECT ?, CEILING(`timestamp` / ?) * ? AS `ts`, ?, $avgFields
        FROM `records`
        WHERE `device_id` = ? AND `timestamp` > ?
        GROUP BY `ts`";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$deviceId, $resolution, $resolution, $resolution, $deviceId, $minRecordTimestamp]);
        $stmt->closeCursor();
    }

    public function getHistoricData($deviceId, $type = 'pm', $range = 'day', $avgType = null) {
        switch ($type) {
            case 'temperature':
            $fields = array('temperature', 'heater_temperature');
            break;
        
            case 'pressure':
            $fields = array('pressure', 'temperature');
            break;
        
            case 'humidity':
            $fields = array('humidity', 'heater_humidity');
            break;
        
            case 'pm':
            $fields = array('pm10', 'pm25', 'pm1', 'pm4');
            break;

            case 'pm_n':
            $fields = array('n05', 'n1', 'n25', 'n4', 'n10');
            break;
    
            case 'co2':
            $fields = array('co2');
            break;
    
            default:
            break;
        }

        $sql_fields = array();
        foreach ($fields as $f) {
            $sql_fields[] = "`$f`";
        }
        $sql_fields = implode(',', $sql_fields);

        $now = time();
        switch ($range) {
            case 'week':
            $since = $now - (7 * 24 + $avgType) * 60 * 60;
            $resolution = 180 * 7;
            break;
        
            case 'month':
            $since = $now - 30 * 24 * 60 * 60;
            $resolution = 180 * 30;
            break;
        
            case 'year':
            $since = $now - 365 * 24 * 60 * 60;
            $resolution = 180 * 365;
            break;

            default:
            case 'day':
            $since = $now - (24 + $avgType) * 60 * 60;
            $resolution = 180;
            break;
        }
        $since = ceil($since / $resolution) * $resolution;

        $data = array();
        for ($i = $since; $i <= $now; $i += $resolution) {
            foreach ($fields as $f) {
                $data[$f][$i] = null;
            }
        }

        $stmt = $this->pdo->prepare("SELECT `timestamp` AS `ts`, $sql_fields FROM `aggregates` WHERE `device_id` = ? AND `timestamp` >= ? AND `resolution` = ? ORDER BY `ts` ASC");
        $stmt->execute([$deviceId, $since, $resolution]);
        while ($row = $stmt->fetch()) {
            foreach ($fields as $f) {
                $data[$f][$row['ts']] = $row[$f];
            }
        }
        $stmt->closeCursor();

        if ($avgType !== null) {
            foreach ($data as $k => $values) {
                $data[$k] = RecordModel::transformToWalkingAverage($values, $avgType);
            }
            $since += 60 * 60 * $avgType;
        }

        if (isset($data['pressure'])) {
            $device = $this->deviceModel->getDeviceById($deviceId);
            foreach ($data['pressure'] as $ts => $v) {
                $data['pressure'][$ts] = RecordModel::normalizePressure($data['pressure'][$ts], $device['elevation'], $data['temperature'][$ts]);
            }
        }
        return array('start' => $since, 'end' => $now, 'data' => $data);
    }

    public function getDailyAverages($deviceId) {
        $stmt = $this->pdo->prepare(
            "SELECT
                `pm10` AS `pm10_avg`,
                `pm25` AS `pm25_avg`
            FROM `aggregates`
            WHERE
                `device_id` = ? AND
                `resolution` = ?
                AND `timestamp` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 YEAR))
            ORDER BY `timestamp`");
        $resolution = 24 * 60 * 60;

        $stmt->execute([$deviceId, $resolution]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    private static function transformToWalkingAverage($data, $walkingAverageHours) {
        $data_array = array();
        foreach ($data as $ts => $v) {
            $data_array[] = array('ts' => $ts, 'v' => $v);
        }
        $data = $data_array;
        $result = array();
        $data_size = count($data);

        $j = null;
        $sum = 0;
        $count = 0;
        for ($j = 0; $j < $data_size; $j++) {
            if (($data[$j]['ts'] - $data[0]['ts']) >= $walkingAverageHours * 60 * 60) {
                break;
            }
            if ($data[$j]['v'] != null) {
                $sum += $data[$j]['v'];
                $count++;
            }
        }

        $i = 0;
        for ($j--; $j < $data_size; $j++) {
            if ($data[$j]['v'] == null) {
                $result[$data[$j]['ts']] = null;
            } else {
                $sum += $data[$j]['v'];
                $count++;
                $result[$data[$j]['ts']] = $sum / $count;
            }
            if ($data[$i]['v'] != null) {
                $sum -= $data[$i]['v'];
                $count--;
            }
            $i++;
        }
        return $result;
    }

    public function getAverages($deviceId, $currentAvgType = '1') {
        if ($currentAvgType == '1') {
            $averages = $this->getLastAvg($deviceId, 1);
            $pm10_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM10_THRESHOLDS_1H;
            $pm25_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM25_THRESHOLDS_1H;
            $pm10_limit = \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_1H;
            $pm25_limit = \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_1H;
        } else {
            $averages = $this->getLastAvg($deviceId, 24);
            $pm10_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM10_THRESHOLDS_24H;
            $pm25_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM25_THRESHOLDS_24H;
            $pm10_limit = \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_24H;
            $pm25_limit = \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_24H;
        }
    
        if ($averages['pm10'] === null) {
            $pm10_level = null;
            $rel_pm10 = null;
        } else {
            $pm10_level = \AirQualityInfo\Lib\PollutionLevel::findLevel($pm10_thresholds, $averages['pm10']);
            $rel_pm10 = 100 * $averages['pm10'] / $pm10_limit;
        }
    
        if ($averages['pm25'] === null) {
            $pm25_level = null;
            $rel_pm25 = null;
        } else {
            $pm25_level = \AirQualityInfo\Lib\PollutionLevel::findLevel($pm25_thresholds, $averages['pm25']);
            $rel_pm25 = 100 * $averages['pm25'] / $pm25_limit;
        }
    
        if ($pm10_level === null && $pm25_level === null) {
            $max_level = null;
        } else {
            $max_level = max($pm10_level, $pm25_level);
        }
    
        return array(
            'values' => $averages,
            'pm25_level' => $pm25_level,
            'pm10_level' => $pm10_level,
            'max_level' => $max_level,
            'rel_pm25' => $rel_pm25,
            'rel_pm10' => $rel_pm10,
        );
    }

    // https://pl.wikipedia.org/wiki/Wz%C3%B3r_barometryczny
    private static function normalizePressure($pressure, $h, $temp) {
        if ($pressure === null) {
            return null;
        }
        $pressure = (float)$pressure;
        if ($h === null) {
            return $pressure;
        }
        $h = (int)$h;
        $mu = 0.0289644;
        $g = 9.80665;
        $R = 8.3144598;
        $T = $temp + 273.15;
        $normalized = $pressure / exp(- $mu * $g * $h / ($R * $T));
        return $normalized;
    }
}
?>