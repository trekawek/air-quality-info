<?php

if (!($_SERVER['PHP_AUTH_USER'] == $device['user'] && $_SERVER['PHP_AUTH_PW'] == $device['password'])) {
    header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}

function append_to_data($result, &$data) {
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

function row_is_empty($row) {
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

function migrate_rrd_to_mysql($rrd_file, $dao) {
    $esp8266id = basename($rrd_file, '.rrd');

    $data = array();
    $end = append_to_data(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-2d', '--end=now')), $data)[1];
    append_to_data(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-2w', '--end=now-2d')), $data);
    append_to_data(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-62d', '--end=now-2w')), $data);
    $start = append_to_data(rrd_fetch($rrd_file, array("AVERAGE", '--start=now-2y', '--end=now-62d')), $data)[0];

    $previous_row = null;
    for ($ts = $start; $ts <= $end; $ts += 180) {
        $row = (isset($data[$ts]) ? $data[$ts] : array());
        if (row_is_empty($row) && $previous_row === null) {
            continue;
        }

        foreach ($row as $i => $v) {
            if ($v === null && $previous_row !== null) {
                $row[$i] = $previous_row[$i];
            }
        }
        $previous_row = $row;
        $dao->update($ts, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6]);
    }
}

$esp8266id = $device['esp8266id'];
$rrd_file = __DIR__ . "/../data/${esp8266id}.rrd";
echo "Migrating $esp8266id.rrd...\n";
migrate_rrd_to_mysql($rrd_file, $dao);
echo "Done.\n";

?>