<?php
namespace AirQualityInfo\Model;

class RecordModel {

    const FIELDS = array('pm25','pm10','temperature','pressure','humidity','heater_temperature','heater_humidity');

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    private function getTimestampRange($deviceId) {
        $stmt = $this->mysqli->prepare("SELECT MIN(`timestamp`), MAX(`timestamp`) FROM `records` WHERE `device_id` = ?");
        $stmt->bind_param('i', $deviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array(null, null);
        if ($row = $result->fetch_row()) {
            $data = $row;
        }
        $stmt->close();
        return $data;
    }

    private function insertEmptyRows($deviceId, $recordTimestamp) {
        list($firstTs, $lastTs) = $this->getTimestampRange($deviceId);
        $rangeFrom = null;
        $rangeTo = null;
        if ($firstTs !== null && $recordTimestamp < $firstTs) { // before first timestamp
            $rangeFrom = $recordTimestamp;
            $rangeTo = $firstTs - 180;
        } else if ($lastTs !== null && $recordTimestamp > $lastTs) { // after last timestamp
            $rangeFrom = $lastTs + 180;
            $rangeTo = $recordTimestamp;
        } else if ($firstTs === null && $lastTs === null) { // first entry
            $rangeFrom = $recordTimestamp;
            $rangeTo = $recordTimestamp;
        }
        
        if ($rangeFrom !== null && $rangeTo !== null) {
            $insertStmt = $this->mysqli->prepare("INSERT INTO `records` (`timestamp`, `device_id`) VALUES (?, ?)");
            for ($ts = $rangeFrom; $ts <= $rangeTo; $ts += 180) {
                $insertStmt->bind_param('ii', $ts, $deviceId);
                $insertStmt->execute();
            }
            $insertStmt->close();
        }
    }

    public function update($deviceId, $timestamp, $pm25, $pm10, $temp, $press, $hum, $heaterTemp, $heaterHum) {
        $recordTimestamp = floor($timestamp / 180) * 180;
        $this->insertEmptyRows($deviceId, $recordTimestamp);

        $record = array (
            'pm25' => $pm25,
            'pm10' => $pm10,
            'temperature' => $temp,
            'pressure' => $press,
            'humidity' => $hum,
            'heater_temperature' => $heaterTemp,
            'heater_humidity' => $heaterHum
        );
        foreach ($record as $k => $v) {
            if (is_nan($v)) {
                $record[$k] = null;
            }
        }

        // fill empty properties for the last 6 minutes
        $updateSql = "UPDATE `records` SET ";
        $updates = array();
        $params = array();
        foreach ($record as $k => $v) {
            $updates[] = "`$k` = IFNULL(`$k`, ?)";
            $params[] = $v;
        }
        $updateSql .= implode(", ", $updates);
        $updateSql .= "WHERE `timestamp` in (?, ?) AND `device_id` = ?";
        $params[] = $recordTimestamp;
        $params[] = $recordTimestamp - 180;
        $params[] = $deviceId;

        $updateStmt = $this->mysqli->prepare($updateSql);
        $pattern = str_repeat('s', count($record));
        $pattern .= 'iii';
        $updateStmt->bind_param($pattern, ...$params);
        $updateStmt->execute();
        $updateStmt->close();
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
            $sql_fields[] = "AVG($f) AS $f";
        }
        $sql_fields = implode(',', $sql_fields);

        $now = time();
        switch ($range) {
            case 'week':
            $since = $now - (7 * 24 + $avgType) * 60 * 60;
            $step = 180 * 7;
            break;
        
            case 'month':
            $since = $now - 30 * 24 * 60 * 60;
            $step = 180 * 30;
            break;
        
            case 'year':
            $since = $now - 365 * 24 * 60 * 60;
            $step = 180 * 365;
            break;

            default:
            case 'day':
            $since = $now - (24 + $avgType) * 60 * 60;
            $step = 180;
            break;
        }
        $since = ceil($since / $step) * $step;

        $data = array();
        for ($i = $since; $i <= $now; $i += $step) {
            foreach ($fields as $f) {
                $data[$f][$i] = null;
            }
        }

        $stmt = $this->mysqli->prepare("SELECT CEILING(`timestamp` / $step) * $step AS `ts`, $sql_fields FROM `records` WHERE `device_id` = ? AND `timestamp` >= ? GROUP BY `ts` ORDER BY `ts` ASC");
        $stmt->bind_param('ii', $deviceId, $since);
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
                AVG(`pm10`) AS `pm10_avg`,
                AVG(`pm25`) AS `pm25_avg`,
                DATE(FROM_UNIXTIME(`timestamp`)) AS `date`
            FROM `records`
            WHERE
                `device_id` = ?
                AND `timestamp` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 YEAR))
            GROUP BY DATE(FROM_UNIXTIME(`timestamp`))
            ORDER BY `date`");
        $stmt->bind_param('i', $deviceId);

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