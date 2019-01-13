<?php
class MysqlDao implements Dao {

    private $esp8266id;

    private $mysqli;

    private $aggregateTypes;

    private $parameters;

    public function __construct($esp8266id, $mysqli) {
        $this->mysqli = $mysqli;

        $result = $mysqli->query("SELECT * FROM aggregate_types ORDER BY rotation_days");
        $this->aggregateTypes = array();
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
            $this->aggregateTypes[$row['id']] = $row;
        }
        $result->close();

        $result = $mysqli->query("SELECT * FROM parameters ORDER BY id");
        $this->parameters = array();
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $p) {
            $this->parameters[$p['id']] = $p['name'];
        }
        $result->close();
    }

    public function dbExists() {
        return true;
    }

    public function createDb() {
        // do nothing
    }

    public function update($timestamp, $pm25, $pm10, $temp, $press, $hum, $heaterTemp, $heaterHum) {
        $record = array (
            'pm10' => $pm10,
            'pm25' => $pm25,
            'temperature' => $temp,
            'pressure' => $press,
            'humidity' => $hum,
            'heaterTemp' => $heaterTemp,
            'heaterHum' => $heaterHum
        );
        $aggregateTimestamp = ceil($timestamp / $windowSec) * $windowSec;
        foreach ($this->aggregateTypes as $aggregateType) {
            $this->updateAggregate($aggregateType, $timestamp, $aggregateTimestamp, $record);
        }
        $this->compactAggregates($aggregateTimestamp);
    }

    public function updateAggregate($aggregateType, $recordTimestamp, $aggregateTimestamp, $record) {
        $windowSec = $aggregateType['window_size_minutes'] * 60;

        $stmt = $mysqli->prepare("SELECT id, `partial` FROM aggregates WHERE `timestamp` = ? AND aggregate_type_id = ? AND esp8266id = ?");
        $stmt->bind_param('ddd', $aggregateType['id'], $nextTimestamp, $this->esp8266id);
        $stmt->execute();
        $stmt->bind_result($aggregateId, $partial);
        if ($stmt->fetch()) {
            if (!$partial) {
                // this aggregate has been already compacted
                return;
            }
        } else {
            $insertStmt = $mysqli->prepare("INSERT INTO aggregates (`timestamp`, aggregate_type_id, `partial`, esp8266id) VALUES (?, ?, 1, ?)");
            $insertStmt->bind_param('ddd', $aggregateTimestamp, $aggregateTypes['id'], $this->esp8266id);
            $insertStmt->execute();
            $aggregateId = $insertStmt->insert_id;
            $insertStmt->close();
        }
        $stmt->close();

        $this->insertRecords($aggregateId, $recordTimestamp, $record);
    }

    public function insertRecords($aggregateId, $recordTimestamp, $record) {
        $insertStmt = $mysqli->prepare("INSERT INTO records (`timestamp`, aggregate_id) VALUES (?, ?)");
        $insertStmt->bind_param('dd', $recordTimestamp, $aggregateId);
        $insertStmt->execute();
        $recordId = $insertStmt->insert_id;
        $insertStmt->close();

        $insertStmt = $mysqli->prepare("INSERT INTO record_values (record_id, parameter_id, `value`) VALUES (?, ?, ?)");
        foreach ($parameters as $id => $name) {
            if (isset($record[$name])) {
                $insertStmt->bind_param('dds', $recordId, $id, $record[$name]);
                $insertStmt->execute();    
            }
        }
        $insertStmt->close();
    }

    public function deleteRecords($aggregateId) {
        $deleteStmt = $mysqli->prepare("DELETE FROM records VALUES aggregate_id = ?");
        $deleteStmt->bind_param('d', $aggregateId);
        $deleteStmt->execute();
    }

    public function compactAggregates($aggregateTimestamp) {
        $stmt = $mysqli->prepare("SELECT id, aggregate_type_id FROM aggregates WHERE `timestamp` <= ? AND `partial` = 1 AND esp8266id = ?");
        $stmt->bind_param('dd', $aggregateTimestamp, $this->esp8266id);
        $stmt->execute();
        $result = $stmt->get_result();
        foreach ($result->fetch_all() as $aggregate) {
            $records = getAggregateRecords($aggregate[0]);
            $aggregateType = $this->aggregateTypes[$aggregate[1]];
            $aggregatedRecords = $this->compactRecords($aggregateType, $records, $aggregateTimestamp);
            $this->deleteRecords($aggregateId);
            $this->insertRecords($aggregateTimestamp, $aggregatedRecords);
            
            $updateStmt = $mysqli->prepare("UPDATE aggregates SET `partial` = 0 WHERE id = ?");
            $updateStmt->bind_param('d', $aggregate[0]);
            $updateStmt->execute();
            $updateStmt->close();
        }
        $stmt->close();
    }

    /** returns => [ 'ts1' => [ 'pm10' => 1, 'pm25' => 5, ... ], ...] */
    public function getAggregateRecords($aggregateId) {
        $stmt = $mysqli->prepare("SELECT records.`timestamp`, parameter_id, `value` FROM records LEFT JOIN record_values ON records.id = recovrd_values.record_id WHERE records.aggregate_id = ?");
        $stmt->bind_param('dd', $aggregateId);
        $stmt->execute();
        $result = $stmt->get_result();
        $records = array();
        foreach ($result->fetch_all() as $r) {
            $timestamp = $r[0];
            $parameter = $this->parameters[$r[1]];
            $value = $r[2];
            if (!isset($records[$timestamp])) {
                $records[$timestamp] = array();
            }
            $records[$timestamp][$parameter] = $value;
        }
        $stmt->close();
        return $records;
    }

    public function compactRecords($aggregateType, $records, $toTimestamp) {
        switch ($aggregateType['type']) {
            case 'average':
            default:
            return averageAggregate($aggregateType, $records, $toTimestamp);
        }
    }

    public function averageAggregate($aggregateType, $records, $toTimestamp) {
        $window = $aggregateType['window_size_minutes'] * 60;
        $fromTimestamp = $toTimestamp - $window;
        
        $timestamps = array_keys($records);
        $firstTimestamp = $timestamps[0];
        $lastTimestamp = $timestamps[count($timestamps) - 1];
        
        $avg = array();
        $avg = vecMultiplyAndSum($avg, $records[$firstTimestamp], ($firstTimestamp - $fromTimestamp) / $window);
        $avg = vecMultiplyAndSum($avg, $records[$lastTimestamp], ($toTimestamp - $lastTimestamp) / $window);
        for ($i = 1; $i < count($timestamps); $i++) {
            $avg = vecMultiplyAndSum($avg, $records[$timestamps[$i - 1]], ($timestamps[$i] - $timestamps[$i - 1]) / $window);
        }
        return $avg;
    }

    /** returns acc + arr * scalar */
    public static function vecMultiplyAndSum($acc, $arr, $scalar) {
        foreach ($arr as $k => $v) {
            $v *= $scalar;
            if (isset($acc[$k])) {
                $acc[$k] += $v;
            } else {
                $acc[$k] = $v;
            }
        }
        return $acc;
    }

    public function getLastData() {
        
    }

    public function getLastAvg($avgType) {

    }

    public function getHistoricData($type = 'pm', $range = 'day', $avgType = null) {
        
    }

}
?>
