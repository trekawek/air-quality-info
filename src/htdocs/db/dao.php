<?php
interface Dao {

    public function dbExists();

    public function createDb();

    public function update($time, $pm25, $pm10, $temp, $press, $hum, $heaterTemp, $heaterHum);

    public function getLastData();

    public function getLastAvg($avgType);

    public function getHistoricData($type = 'pm', $range = 'day', $avgType = null);

}
?>