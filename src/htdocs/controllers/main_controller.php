<?php
class MainController extends AbstractController {

    private $dao;

    private $currentLocale;

    public function __construct($dao, $currentLocale) {
        parent::__construct();
        $this->dao = $dao;
        $this->currentLocale = $currentLocale;
    }

    public function index($device) {
        $this->render(array('view' => 'views/index.php'));
    }

    public function index_inner($device) {
        $currentAvgType = $_GET['avgType'];
        $lastData = $this->dao->getLastData($device['esp8266id']);
        $averages = $this->getAverages($device['esp8266id'], $currentAvgType);
        $this->render(array('view' => 'views/index_inner.php', 'layout' => false), array(
            'averages' => $averages,
            'currentAvgType' => $currentAvgType,
            'sensors' => $lastData
        ));
    }

    public function all($deviceGroupId) {
        $nodeById = $this->deviceTree['nodeById'];
        if (!isset($nodeById[$deviceGroupId])) {
            $deviceGroupId = 0;
        }
        $node = $nodeById[$deviceGroupId];
        $devices = Navigation::parseTree($node);
        
        $data = array();
        foreach ($devices as $device_name) {
            foreach (CONFIG['devices'] as $device) {
                if ($device['name'] == $device_name) {
                    break;
                }
            }
        
            $sensors = $this->dao->getLastData($device['esp8266id']);
            $currentAvgType = '1';
            if (isset($_GET['avgType']) && $_GET['avgType'] == '24') {
                $currentAvgType = '24';
            }
            $averages = $this->getAverages($device['esp8266id'], $currentAvgType);
            $desc = array_map('trim', explode('/', $device['description']));

            $data[] = array('sensors' => $sensors, 'averages' => $averages, 'desc' => $desc, 'device' => $device);
        }
        
        $this->render(array('view' => 'views/all_sensors.php'), array(
            'deviceGroupId' => $deviceGroupId,
            'data' => $data,
            'currentAvgType' => $currentAvgType
        ));
    }

    private function getAverages($esp8266id, $currentAvgType = '1') {
        if ($currentAvgType == '1') {
            $averages = $this->dao->getLastAvg($esp8266id, 1);
            $pm10_thresholds = PM10_THRESHOLDS_1H;
            $pm25_thresholds = PM25_THRESHOLDS_1H;
            $pm10_limit = PM10_LIMIT_1H;
            $pm25_limit = PM25_LIMIT_1H;
        } else {
            $averages = $this->dao->getLastAvg($esp8266id, 24);
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
    
}

?>