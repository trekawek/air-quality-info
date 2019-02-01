<?php
function getAverages($dao, $sensors, $current_avg_type = '1') {
    if ($current_avg_type == '1') {
        $averages = $dao->getLastAvg(1);
        $pm10_thresholds = PM10_THRESHOLDS_1H;
        $pm25_thresholds = PM25_THRESHOLDS_1H;
        $pm10_limit = PM10_LIMIT_1H;
        $pm25_limit = PM25_LIMIT_1H;
    } else {
        $averages = $dao->getLastAvg(24);
        $pm10_thresholds = PM10_THRESHOLDS_24H;
        $pm25_thresholds = PM25_THRESHOLDS_24H;
        $pm10_limit = PM10_LIMIT_24H;
        $pm25_limit = PM25_LIMIT_24H;
    }

    if ($averages['pm10'] === null) {
        $pm10_level = null;
        $rel_pm10 = null;
    } else {
        $pm10_level = find_level($pm10_thresholds, $averages['pm10']);
        $rel_pm10 = 100 * $averages['pm10'] / $pm10_limit;
    }

    if ($averages['pm25'] === null) {
        $pm25_level = null;
        $rel_pm25 = null;
    } else {
        $pm25_level = find_level($pm25_thresholds, $averages['pm25']);
        $rel_pm25 = 100 * $averages['pm25'] / $pm25_limit;
    }

    if ($pm10_level === null && $pm25_level === null) {
        $max_level = null;
    } else {
        $max_level = max($pm10_level, $pm25_level);
    }

    return array(
        'values' => $averages,
        'pm25_level' => $pm25_level,
        'pm10_level' => $pm10_level,
        'max_level' => $max_level,
        'rel_pm25' => $rel_pm25,
        'rel_pm10' => $rel_pm10,
    );
}
?>