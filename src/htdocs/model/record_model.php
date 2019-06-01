<?php
namespace AirQualityInfo\Model;

class RecordModel {

    const FIELDS = array('pm25','pm10','temperature','pressure','humidity','heater_temperature','heater_humidity');

    const AGGREGATES = array(
        array('resolution' => 1 * 180,      'ttl' => 24 * 60 * 60),
        array('resolution' => 7 * 180,      'ttl' => 7 * 24 * 60 * 60),
        array('resolution' => 30 * 180,     'ttl' => 30 * 24 * 60 * 60),
        array('resolution' => 365 * 180,    'ttl' => 365 * 24 * 60 * 60),
        array('resolution' => 24 * 60 * 60, 'ttl' => null)
    );

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    private function getLastTimestamp($deviceId) {
        $stmt = $this->mysqli->prepare("SELECT MAX(`timestamp`) FROM `records` WHERE `device_id` = ?");
        $stmt->bind_param('i', $deviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $lastTs = null;
        if ($row = $result->fetch_row()) {
            $data = $row[0];
        }
        $stmt->close();
        return $lastTs;
    }

    //public function update($deviceId, ) {
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
        $lastTs = $this->getLastTimestamp($deviceId);

        $sql = 'INSERT INTO `records` (`device_id`, `timestamp`, ';
        foreach (RecordModel::FIELDS as $f) {
            $sql .= "`$f`, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ') VALUES (?, ?, ';
        $sql .= str_repeat('?, ', count(RecordModel::FIELDS));
        $sql = substr($sql, 0, -2);
        $sql .= ')';

        $insertStmt = $this->mysqli->prepare($sql);
        $typedef = 'ii'.str_repeat('s', count(RecordModel::FIELDS));
        
        $minTimestamp = null;
        foreach ($records as $i => $record) {
            $record['timestamp'] = floor($record['timestamp'] / 180) * 180;
            if ($minTimestamp === null || $minTimestamp > $record['timestamp']) {
                $minTimestamp = $record['timestamp'];
            }

            foreach ($record as $k => $v) {
                if (is_nan($v)) {
                    $record[$k] = null;
                }
            }
            if ($record['pressure'] !== null) {
                $record['pressure'] /= 100;
            }

            $param = array($deviceId, $record['timestamp']);
            foreach (RecordModel::FIELDS as $f) {
                $param[] = $record[$f];
            }
            $insertStmt->bind_param($typedef, ...$param);
            $insertStmt->execute();

            // fill the gaps with nulls
            if ($lastTs !== null) {
                for ($i = $lastTs + 180; $i < ($record['timestamp'] - 180); $i += 180) {
                    $param = array($deviceId, $i);
                    foreach (RecordModel::FIELDS as $f) {
                        $param[] = null;
                    }
                    $insertStmt->bind_param($typedef, ...$param);
                    $insertStmt->execute();
                }
            }

            $lastTs = $record['timestamp'];
            
        }
        $insertStmt->close();

        $this->createAggregates($deviceId, $minTimestamp);
    }

    public function getLastData($deviceId) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `records` WHERE `device_id` = ? AND `pm10` IS NOT NULL ORDER BY `timestamp` DESC LIMIT 1");
        $stmt->bind_param('i', $deviceId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $result->close();

        $data = array('last_update' => $row['timestamp']);
        foreach (RecordModel::FIELDS as $f) {
            $data[$f] = $row[$f];
        }
        return $data;
    }

    public function getLastAvg($deviceId, $hours) {
        $fields = array();
        foreach (RecordModel::FIELDS as $f) {
            $fields[] = "AVG($f)";
        }
        $fields = implode(",", $fields);

        $stmt = $this->mysqli->prepare("SELECT $fields FROM `records` WHERE `device_id` = ? AND `timestamp` >= ?");
        $since = time() - $hours * 60 * 60;
        $stmt->bind_param('ii', $deviceId, $since);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_row();
        $result->close();

        $data = array();
        foreach (RecordModel::FIELDS as $i => $f) {
            $data[$f] = $row[$i];
        }
        return $data;
    }

    public function createAggregates($deviceId, $fromTs) {
        foreach (RecordModel::AGGREGATES as $aggregate) {
            $this->updateAggregate($deviceId, $aggregate['resolution'], $fromTs);
            if ($aggregate['ttl'] !== null) {
                $stmt = $this->mysqli->prepare("DELETE FROM `aggregates` WHERE `device_id` = ? AND `timestamp` < ? AND `resolution` = ?");
                $minTimestamp = time() - $aggregate['ttl'];
                $stmt->bind_param('iii', $deviceId, $minTimestamp, $aggregate['resolution']);
                $stmt->execute();
                $stmt->close();
            }
        }
        /*$stmt = $this->mysqli->prepare("DELETE FROM `records` WHERE `device_id` = ? AND `timestamp` < ?");
        $minTimestamp = time() - end(RecordModel::AGGREGATES['resolution']);
        $stmt->bind_param('ii', $minTimestamp);
        $stmt->execute();
        $stmt->close();*/
    }

    private function updateAggregate($deviceId, $resolution, $fromTs) {
        error_log("Updating aggregate for device $deviceId with resolution $resolution from timestamp $fromTs");
        $avgFields = array();
        $fields = array();
        foreach (RecordModel::FIELDS as $f) {
            $avgFields[] = "AVG(`$f`)";
            $fields[] = "`$f`";
        }
        $avgFields = implode(",", $avgFields);
        $fields = implode(",", $fields);
    
        $roundedTimestamp = ceil($fromTs / $resolution) * $resolution;

        $stmt = $this->mysqli->prepare("DELETE FROM `aggregates` WHERE `device_id` = ? AND `timestamp` >= ? AND `resolution` = ?");
        $stmt->bind_param('iii', $deviceId, $roundedTimestamp, $resolution);
        $stmt->execute();
        $stmt->close();
        
        $sql = "INSERT INTO `aggregates` (`device_id`, `timestamp`, `resolution`, $fields)        
        SELECT ?, CEILING(`timestamp` / ?) * ? AS `ts`, ?, $avgFields
        FROM `records`
        WHERE `device_id` = ? AND `timestamp` >= ?
        GROUP BY `ts`";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iiiiii', $deviceId, $resolution, $resolution, $resolution, $deviceId, $roundedTimestamp);
        $stmt->execute();
        $stmt->close();
    }

    public function getHistoricData($deviceId, $type = 'pm', $range = 'day', $avgType = null) {
        switch ($type) {
            case 'temperature':
            $fields = array('temperature', 'heater_temperature');
            break;
        
            case 'pressure':
            $fields = array('pressure');
            break;
        
            case 'humidity':
            $fields = array('humidity', 'heater_humidity');
            break;
        
            case 'pm':
            $fields = array('pm10', 'pm25');
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

        $stmt = $this->mysqli->prepare("SELECT `timestamp` AS `ts`, $sql_fields FROM `aggregates` WHERE `device_id` = ? AND `timestamp` >= ? AND `resolution` = ? ORDER BY `ts` ASC");
        $stmt->bind_param('iii', $deviceId, $since, $resolution);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            foreach ($fields as $f) {
                $data[$f][$row['ts']] = $row[$f];
            }
        }
        $result->close();

        if ($avgType !== null) {
            foreach ($data as $k => $values) {
                $data[$k] = RecordModel::transformToWalkingAverage($values, $avgType);
            }
            $since += 60 * 60 * $avgType;
        }

        return array('start' => $since, 'end' => $now, 'data' => $data);
    }

    public function getDailyAverages($deviceId) {
        $stmt = $this->mysqli->prepare(
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
        $stmt->bind_param('ii', $deviceId, $resolution);

        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $result->close();
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

}
?>