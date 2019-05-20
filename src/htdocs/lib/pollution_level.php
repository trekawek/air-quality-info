<?php
namespace AirQualityInfo\Lib;

// http://powietrze.gios.gov.pl/pjp/current
class PollutionLevel {

    const POLLUTION_LEVELS = array(
        array('name' => 'Very low'),
        array('name' => 'Low'),
        array('name' => 'Medium'),
        array('name' => 'High'),
        array('name' => 'Very high'),
    );

    const PM10_THRESHOLDS_1H = array(0, 25, 50, 90, 180);

    const PM10_THRESHOLDS_24H = array(0, 15, 30, 50, 100);

    const PM10_THRESHOLD_1Y = 40;
    const PM10_DAILY_THRESHOLD_1Y = 50;
    const PM10_DAYS_ABOVE_THRESHOLD = 31;

    const PM25_THRESHOLDS_1H = array(0, 15, 30, 55, 110);
    const PM25_THRESHOLDS_24H = array(0, 10, 20, 30, 60);

    const PM25_DAILY_THRESHOLD_1Y = 30;

    const PM10_LIMIT_1H = 50;
    const PM25_LIMIT_1H = 25;

    const PM10_LIMIT_24H = 50;
    const PM25_LIMIT_24H = 25;

    public static function findLevel($thresholds, $value) {
        if ($value === null) {
            return null;
        }
        foreach ($thresholds as $i => $v) {
            if ($v > $value) {
                return $i - 1;
            }
        }
        return count($thresholds) - 1;
    }
}

?>