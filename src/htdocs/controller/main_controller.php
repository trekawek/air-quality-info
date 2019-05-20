<?php
namespace AirQualityInfo\Controller;

class MainController extends AbstractController {

    private $recordModel;

    private $devices;

    public function __construct(\AirQualityInfo\Model\RecordModel $recordModel, $devices) {
        $this->recordModel = $recordModel;
        $this->devices = $devices;
    }

    public function index($device) {
        $this->render(array('view' => 'views/index.php'));
    }

    public function index_inner($device) {
        $currentAvgType = $_GET['avgType'];
        $lastData = $this->recordModel->getLastData($device['id']);
        $averages = $this->getAverages($device['id'], $currentAvgType);
        $desc = array_map('trim', explode('/', $device['description_path']));
        $this->render(array('view' => 'views/index_inner.php', 'layout' => false), array(
            'averages' => $averages,
            'currentAvgType' => $currentAvgType,
            'sensors' => $lastData,
            'desc' => $desc
        ));
    }

    public function all() {
        $data = array();
        foreach ($this->devices as $device) {
            $sensors = $this->recordModel->getLastData($device['id']);
            $currentAvgType = '1';
            if (isset($_GET['avgType']) && $_GET['avgType'] == '24') {
                $currentAvgType = '24';
            }
            $averages = $this->getAverages($device['id'], $currentAvgType);
            $desc = array_map('trim', explode('/', $device['description']));
            $data[] = array('sensors' => $sensors, 'averages' => $averages, 'desc' => $desc, 'device' => $device);
        }
        
        $this->render(array('view' => 'views/all_sensors.php'), array(
            'data' => $data,
            'currentAvgType' => $currentAvgType
        ));
    }

    private function getAverages($deviceId, $currentAvgType = '1') {
        if ($currentAvgType == '1') {
            $averages = $this->recordModel->getLastAvg($deviceId, 1);
            $pm10_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM10_THRESHOLDS_1H;
            $pm25_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM25_THRESHOLDS_1H;
            $pm10_limit = \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_1H;
            $pm25_limit = \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_1H;
        } else {
            $averages = $this->recordModel->getLastAvg($deviceId, 24);
            $pm10_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM10_THRESHOLDS_24H;
            $pm25_thresholds = \AirQualityInfo\Lib\PollutionLevel::PM25_THRESHOLDS_24H;
            $pm10_limit = \AirQualityInfo\Lib\PollutionLevel::PM10_LIMIT_24H;
            $pm25_limit = \AirQualityInfo\Lib\PollutionLevel::PM25_LIMIT_24H;
        }
    
        if ($averages['pm10'] === null) {
            $pm10_level = null;
            $rel_pm10 = null;
        } else {
            $pm10_level = \AirQualityInfo\Lib\PollutionLevel::findLevel($pm10_thresholds, $averages['pm10']);
            $rel_pm10 = 100 * $averages['pm10'] / $pm10_limit;
        }
    
        if ($averages['pm25'] === null) {
            $pm25_level = null;
            $rel_pm25 = null;
        } else {
            $pm25_level = \AirQualityInfo\Lib\PollutionLevel::findLevel($pm25_thresholds, $averages['pm25']);
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
    
}

?>