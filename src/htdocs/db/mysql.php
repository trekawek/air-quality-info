<?php
class MysqlDao implements Dao {

    const FIELDS = array('pm25','pm10','temperature','pressure','humidity','heater_temperature','heater_humidity');

    private $esp8266id;

    private $mysqli;

    public function __construct($esp8266id, $mysqli) {
        $this->mysqli = $mysqli;
        $this->esp8266id = $esp8266id;
    }

    public function dbExists() {
        return true;
    }

    public function createDb() {
        // do nothing
    }

    public function update($timestamp, $pm25, $pm10, $temp, $press, $hum, $heaterTemp, $heaterHum) {
        $recordTimestamp = floor($timestamp / 180) * 180;

        $stmt = $this->mysqli->prepare("SELECT `timestamp` FROM `records` WHERE `esp8266id` = ? ORDER BY `timestamp` DESC LIMIT 1");
        $stmt->bind_param('i', $this->esp8266id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_row()) {
            $lastTimestamp = $row[0];
        } else {
            // first row
            $lastTimestamp = $recordTimestamp - 180;
        }
        $insertStmt = $this->mysqli->prepare("INSERT INTO `records` (`timestamp`, `esp8266id`) VALUES (?, ?)");
        for ($ts = $lastTimestamp + 180; $ts <= $recordTimestamp; $ts += 180) {
            $insertStmt->bind_param('ii', $ts, $this->esp8266id);
            $insertStmt->execute();
        }
        $insertStmt->close();
        $stmt->close();

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
        $updateSql .= "WHERE `timestamp` in (?, ?) AND `esp8266id` = ?";
        $params[] = $recordTimestamp;
        $params[] = $recordTimestamp - 180;
        $params[] = $this->esp8266id;

        $updateStmt = $this->mysqli->prepare($updateSql);
        $pattern = str_repeat('s', count($record));
        $pattern .= 'iii';
        $updateStmt->bind_param($pattern, ...$params);
        $updateStmt->execute();
        $updateStmt->close();
    }

    public function getLastData() {
        $stmt = $this->mysqli->prepare("SELECT * FROM `records` WHERE `esp8266id` = ? AND `pm10` IS NOT NULL ORDER BY `timestamp` DESC LIMIT 1");
        $stmt->bind_param('i', $this->esp8266id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $result->close();

        $data = array('last_update' => $row['timestamp']);
        foreach (MysqlDao::FIELDS as $f) {
            $data[$f] = $row[$f];
        }
        return $data;
    }

    public function getLastAvg($avgType) {
        $fields = array();
        foreach (MysqlDao::FIELDS as $f) {
            $fields[] = "AVG($f)";
        }
        $fields = implode(",", $fields);

        $stmt = $this->mysqli->prepare("SELECT $fields FROM `records` WHERE `esp8266id` = ? AND `timestamp` >= ?");
        $since = time() - $avgType * 60 * 60;
        $stmt->bind_param('ii', $this->esp8266id, $since);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        $result->close();

        $data = array();
        foreach (MysqlDao::FIELDS as $i => $f) {
            $data[$f] = $row[$i];
        }
        return $data;
    }

    public function getHistoricData($type = 'pm', $range = 'day', $avgType = null) {
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

        $stmt = $this->mysqli->prepare("SELECT CEILING(`timestamp` / $step) * $step AS `ts`, $sql_fields FROM `records` WHERE `esp8266id` = ? AND `timestamp` >= ? GROUP BY `ts` ORDER BY `ts` ASC");
        $stmt->bind_param('ii', $this->esp8266id, $since);
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
                $data[$k] = transform_to_walking_average($values, $avgType);
            }
            $since += 60 * 60 * $avgType;
        }

        return array('start' => $since, 'end' => $now, 'data' => $data);
    }

    private function jsonUpdateTableExists() {
        $result = $this->mysqli->query("SHOW TABLES LIKE 'json_updates'");
        $table_exists = $result->num_rows > 0;
        $result->close();
        return $table_exists;
    }

    public function logJsonUpdate($time, $json) {
        if (!$this->jsonUpdateTableExists()) {
            return;
        }

        $insertStmt = $this->mysqli->prepare("INSERT INTO `json_updates` (`timestamp`, `esp8266id`, `data`) VALUES (?, ?, ?)");
        $insertStmt->bind_param('iis', $time, $this->esp8266id, $json);
        $insertStmt->execute();
        $insertStmt->close();

        $before = $time - 24 * 60 * 60;
        $deleteStmt = $this->mysqli->prepare("DELETE FROM `json_updates` WHERE `timestamp` < ? AND `esp8266id` = ?");
        $deleteStmt->bind_param('ii', $before, $this->esp8266id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }

    public function getJsonUpdates() {
        $result = array();
        if (!$this->jsonUpdateTableExists()) {
            return $result;
        }

        $stmt = $this->mysqli->prepare("SELECT `timestamp`, `data` FROM `json_updates` WHERE `esp8266id` = ? ORDER BY `timestamp` DESC");
        $stmt->bind_param('i', $this->esp8266id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_row()) {
            $data[$row[0]] = $row[1];
        }
        $stmt->close();
        return $data;
    }

    public function getJsonUpdate($ts) {
        if (!$this->jsonUpdateTableExists()) {
            return null;
        }

        $stmt = $this->mysqli->prepare("SELECT `data` FROM `json_updates` WHERE `esp8266id` = ? AND `timestamp` = ?");
        $stmt->bind_param('ii', $this->esp8266id, $ts);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = null;
        if ($row = $result->fetch_row()) {
            $data = $row[0];
        }
        $stmt->close();
        return $data;
    }

}
?>
