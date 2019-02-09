<?php
class RrdToMysqlMigrator {

    private $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function migrate($device) {
        ini_set('memory_limit', '256M');

        $esp8266id = $device['esp8266id'];
        $rrd_file = __DIR__ . "/../../data/${esp8266id}.rrd";
        echo "Migrating $esp8266id.rrd...\n";
        flush();
        $esp8266id = basename($rrd_file, '.rrd');
    
        $data = array();
        $end = RrdToMysqlMigrator::appendToData(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-2d', '--end=now')), $data)[1];
        RrdToMysqlMigrator::appendToData(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-2w', '--end=now-2d')), $data);
        RrdToMysqlMigrator::appendToData(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-62d', '--end=now-2w')), $data);
        $start = RrdToMysqlMigrator::appendToData(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-2y', '--end=now-62d')), $data)[0];
    
        $previous_row = null;
        for ($ts = $start; $ts <= $end; $ts += 180) {
            $row = (isset($data[$ts]) ? $data[$ts] : array());
            if (RrdToMysqlMigrator::rowIsEmpty($row) && $previous_row === null) {
                continue;
            }
            foreach ($row as $i => $v) {
                if ($v === null && $previous_row !== null) {
                    $row[$i] = $previous_row[$i];
                }
            }
            $previous_row = $row;
            $this->dao->update($esp8266id, $ts, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6]);
        }
        echo "Done.\n";
        flush();
    }

    private static function appendToData($result, &$data) {
        $field_names = array('PM25', 'PM10', 'TEMPERATURE', 'PRESSURE', 'HUMIDITY', 'HEATER_TEMPERATURE', 'HEATER_HUMIDITY');
        $timestamps = array_keys($result['data'][$field_names[0]]);
        $start = $timestamps[0];
        $end = $timestamps[count($timestamps) - 1];
        for ($ts = $start; $ts <= $end; $ts += 180) {
            $row = array();
            foreach ($field_names as $f) {
                if (isset($result['data'][$f][$ts])) {
                    $v = $result['data'][$f][$ts];
                    array_push($row, is_nan($v) ? null : $v);
                } else {
                    array_push($row, null);
                }
            }
            $data[$ts] = $row;
        }
        return array($start, $end);
    }
    
    private static function rowIsEmpty($row) {
        if ($row === null) {
            return true;
        }
        $row_empty = true;
        foreach ($row as $v) {
            if ($v !== null) {
                $row_empty = false;
            }
        }
        return $row_empty;
    }

}
?>