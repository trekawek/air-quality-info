<?php
class AnnualStatsController extends AbstractController {

    private $dao;

    public function __construct($dao) {
        parent::__construct();
        $this->dao = $dao;
    }

    public function index($device) {
        $averages = $this->dao->getLastAvg($device['esp8266id'], 60 * 24 * 365);
        $this->render(array('view' => 'views/annual_stats.php'), array(
            'averages' => $averages
        ));
    }

    public function get_data($device) {
        $averages = $this->dao->getDailyAverages($device['esp8266id']);

        $pm10_days_by_levels = array(0, 0, 0, 0, 0);
        $pm25_days_by_levels = array(0, 0, 0, 0, 0);

        foreach ($averages as $day) {
            $pm10_level = find_level(PM10_THRESHOLDS_24H, $day['pm10_avg']);
            $pm25_level = find_level(PM25_THRESHOLDS_24H, $day['pm25_avg']);
            $pm10_days_by_levels[$pm10_level]++;
            $pm25_days_by_levels[$pm25_level]++;
        }

        $data = array();
        $data['pm10']['days_by_levels'] = $pm10_days_by_levels;
        $data['pm25']['days_by_levels'] = $pm25_days_by_levels;
        $data['pm10']['levels'] = PM10_THRESHOLDS_24H;
        $data['pm25']['levels'] = PM25_THRESHOLDS_24H;
        $data['level_names'] = array_map(function($e) { return $e['name']; }, POLLUTION_LEVELS);
        
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($data);
    }
}
?>