<?php
class RRRDao implements Dao {

    function dbExists($esp8266id) {
        return file_exists($this->getRrdFile($esp8266id));
    }

    function createDb($esp8266id) {
        $options = array(
            '--step=3m',
            'DS:PM25:GAUGE:5m:0:1000',
            'DS:PM10:GAUGE:5m:0:1000',
            'DS:TEMPERATURE:GAUGE:5m:-100:100',
            'DS:PRESSURE:GAUGE:5m:900:1100',
            'DS:HUMIDITY:GAUGE:5m:0:100',
            'DS:HEATER_TEMPERATURE:GAUGE:5m:-100:100',
            'DS:HEATER_HUMIDITY:GAUGE:5m:0:100',
            'RRA:AVERAGE:0.5:3m:2d',
            'RRA:AVERAGE:0.5:15m:2w',
            'RRA:AVERAGE:0.5:90m:62d',
            'RRA:AVERAGE:0.5:12h:2y'
        );
        if (file_exists($this->getRrdFile($esp8266id))) {
            array_push($options, '--source='.$this->getRrdFile($esp8266id));
        }
        if (!rrd_create($this->getRrdFile($esp8266id), $options)) {
            error_log(rrd_error());
        }
    }

    function update($esp8266id, $time, $pm25, $pm10, $temp, $press, $hum, $heater_temp, $heater_hum) {
        if (!$this->dbExists($esp8266id)) {
            $this->createDb($esp8266id);
        }

        $data = array($time, $pm25, $pm10, $temp, $press, $hum, $heater_temp, $heater_hum);
        foreach ($data as $i => $v) {
            if ($v === null) {
                $data[$i] = 'U';
            }
        }
        $data = implode(':', $data);
        if (!rrd_update($this->getRrdFile($esp8266id), array($data))) {
            echo "$data\n";
            echo rrd_error()."\n";
        }
        return $data;
    }

    public function getLastData($esp8266id) {
        if (!$this->dbExists($esp8266id)) {
            return array();
        }
        $data = rrd_lastupdate($this->getRrdFile($esp8266id));
        $sensors = array('last_update' => $data['last_update']);
        for ($i = 0; $i < $data['ds_cnt']; $i++) {
            $sensors[$data['ds_navm'][$i]] = $data['data'][$i];
        }
        return RRRDao::keysToLower($sensors);
    }
      
    public function getLastAvg($esp8266id, $hours) {
        if (!$this->dbExists($esp8266id)) {
            return array();
        }
        $options = array(
            'AVERAGE',
            '--start=now-'.$hours.'h',
            '--resolution=3m',
            '--end=now');
        $result = rrd_fetch($this->getRrdFile($esp8266id), $options);
        $data = $result['data'];
        foreach ($data as $name => $values) {
            $sum = 0;
            $count = 0;
            foreach ($values as $v) {
                if (!is_nan($v)) {
                    $sum += $v;
                    $count++;
                }
            }
            if ($count == 0) {
                $data[$name] = null;
            } else {
                $data[$name] = $sum / $count;
            }
        }
        $data = RRRDao::keysToLower($data);
        $last_update = $this->getLastData($esp8266id);
        foreach ($data as $k => $v) {
            if ($v === null) {
                $data[$k] = $last_update[$k];
            }
        }
        return $data;
    }

    function getHistoricData($esp8266id, $type = 'pm', $range = 'day', $walking_average_hours = null) {
        if (!$this->dbExists($esp8266id)) {
            return array();
        }
        $options = array('AVERAGE');
        switch ($range) {
            case 'week':
            if ($walking_average_hours !== null) {
                array_push($options, '--start=now-'.($walking_average_hours + 24 * 7).'h', "--resolution=15m");
            } else {
                array_push($options, '--start=now-7d', "--resolution=15m");
            }
            break;
        
            case 'month':
            array_push($options, "--start=now-1m", "--resolution=90m");
            break;
        
            case 'year':
            array_push($options, "--start=now-1y", "--resolution=12h");
            break;
        
            case 'day':
            default:
            if ($walking_average_hours !== null) {
                array_push($options, '--start=now-'.($walking_average_hours + 24).'h', "--resolution=3m");
            } else {
                array_push($options, '--start=now-24h', "--resolution=3m");
            }
            break;
        }
        array_push($options, "--end=now");
      
        $result = rrd_fetch($this->getRrdFile($esp8266id), $options);
        $data = $result['data'];
        
        switch ($type) {
            case 'temperature':
            $fields = array('TEMPERATURE', 'HEATER_TEMPERATURE');
            break;
        
            case 'pressure':
            $fields = array('PRESSURE');
            break;
        
            case 'humidity':
            $fields = array('HUMIDITY', 'HEATER_HUMIDITY');
            break;
        
            case 'pm':
            $fields = array('PM10', 'PM25');
            default:
            break;
        }
      
        $data = array_intersect_key($data, array_flip($fields));
      
        foreach ($data as $k => $values) {
            foreach ($values as $ts => $v) {
                if (is_nan($v)) {
                    $data[$k][$ts] = null;
                }
            }
        }
      
        if ($walking_average_hours !== null) {
            foreach ($data as $k => $values) {
                $data[$k] = transform_to_walking_average($values, $walking_average_hours);
            }
            $result['start'] += 60 * 60 * $walking_average_hours;
        }
        $result['data'] = RRRDao::keysToLower($data);
        return $result;
    }

    private static function keysToLower($data) {
        $result = array();
        foreach ($data as $k => $v) {
            if ($v === 'U') {
                $v = null;
            }
            $result[strtolower($k)] = $v;
        }
        return $result;
    }

    public function logJsonUpdate($esp8266id, $time, $json) {
        file_put_contents($this->getJsonFile($esp8266id), $json);
    }

    public function getJsonUpdates($esp8266id) {
        $result = array();
        if (file_exists($this->getJsonFile($esp8266id))) {
            $ts = filemtime($this->getJsonFile($esp8266id));
            $result[$ts] = file_get_contents($this->getJsonFile($esp8266id));
        }
        return $result;
    }

    public function getJsonUpdate($esp8266id, $ts) {
        $data = $this->getJsonUpdates($esp8266id);
        if (isset($data[$ts])) {
            return $data[$ts];
        } else {
            return null;
        }
    }

    private function getJsonFile($esp8266id) {
        return __DIR__ . "/../data/${esp8266id}.json";
    }

    private function getRrdFile($esp8266id) {
        return __DIR__ . "/../data/${esp8266id}.rrd";
    }

    public function getDailyAverages($esp8266id) {
        return array();
    }
}
?>