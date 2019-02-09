<?php
interface Dao {

    public function dbExists($esp8266id);

    public function createDb($esp8266id);

    public function update($esp8266id, $time, $pm25, $pm10, $temp, $press, $hum, $heaterTemp, $heaterHum);

    public function getLastData($esp8266id);

    public function getLastAvg($esp8266id, $avgType);

    public function getHistoricData($esp8266id, $type = 'pm', $range = 'day', $avgType = null);

    public function logJsonUpdate($esp8266id, $time, $json);

    public function getJsonUpdates($esp8266id);

    public function getJsonUpdate($esp8266id, $ts);

}
?>