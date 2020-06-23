<?php
namespace AirQualityInfo\Controller;

use \AirQualityInfo\Lib\PollutionLevel;

class MainController extends AbstractController {

    private $recordModel;

    private $jsonUpdateModel;

    private $locale;

    private $devices;

    public function __construct(
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\JsonUpdateModel $jsonUpdateModel,
            \AirQualityInfo\Lib\Locale $currentLocale,
            $devices) {
        $this->recordModel = $recordModel;
        $this->jsonUpdateModel = $jsonUpdateModel;
        $this->locale = $currentLocale;
        $this->devices = $devices;
    }

    public function index($device) {
        $this->render(array('view' => 'views/index.php'), array('displayCustomHeader' => true));
    }

    public function index_inner($device) {
        $currentAvgType = $_GET['avgType'];
        $lastData = $this->recordModel->getLastData($device['id']);
        $averages = $this->recordModel->getAverages($device['id'], $currentAvgType);

        $nodeById = $this->deviceHierarchyModel->getAllNodesById($this->userId);
        $path = \AirQualityInfo\Model\DeviceHierarchyModel::calculateDevicePath($nodeById, $device['id']);
        $this->render(array('view' => 'views/index_inner.php', 'layout' => false), array(
            'averages' => $averages,
            'currentAvgType' => $currentAvgType,
            'sensors' => $lastData,
            'device' => $device,
            'breadcrumbs' => $path,
            'homeWidget' => array(
                'level' => $averages['max_level'],
                'locale' => ($averages['max_level'] === null ? array() : $this->locale->getValue('_widgets')[$averages['max_level']]),
                'temperature' => $lastData['temperature'],
                'pressure' => $lastData['pressure'],
                'humidity' => $lastData['humidity'],
            )
        ));
    }

    public function weather($device) {
        $lastData = $this->recordModel->getLastData($device['id']);
        $model = array(
            'homeWidget' => array(
                'temperature' => $lastData['temperature'],
                'pressure' => $lastData['pressure'],
                'humidity' => $lastData['humidity'],
            )
        );
        $this->render(array(
            'view' => 'views/weather.php',
            'head' => 'partials/bare/head.php',
            'tail' => 'partials/bare/tail.php'),
            $model);
    }

    public function data_json($device) {
        $data = array();

        $last = $this->recordModel->getLastData($device['id']);
        $avg1h = $this->recordModel->getAverages($device['id'], 1);
        $avg24h = $this->recordModel->getAverages($device['id'], 24);

        $data['last_data'] = MainController::arr_values_to_float($last);

        $data['average_1h'] = MainController::arr_values_to_float($avg1h['values']);
        if ($avg1h['max_level'] === null) {
            $data['average_1h']['index'] = null;
        } else {
            $data['average_1h']['index'] = __(PollutionLevel::POLLUTION_LEVELS[$avg1h['max_level']]['name']);
            $data['average_1h']['index_num'] = $avg1h['max_level'];
        }

        $data['average_24h'] = MainController::arr_values_to_float($avg24h['values']);
        if ($avg24h['max_level'] === null) {
            $data['average_24h']['index'] = null;
        } else {
            $data['average_24h']['index'] = __(PollutionLevel::POLLUTION_LEVELS[$avg24h['max_level']]['name']);
            $data['average_24h']['index_num'] = $avg24h['max_level'];
        }

        if ($data['last_data'] === null) {
            http_response_code(404);
            die();
        } else {
            header('Content-type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
    }

    private static function arr_values_to_float($values) {
        $result = array_map (function($v) {
            return is_numeric($v) ? round($v, 2) : $v;
        }, $values);
        $result = array_filter ($result, function($v) {
            return $v !== null;
        });
        return $result;
    }

    public function all($nodeId = null) {
        if ($nodeId === null) {
            $nodeId = $this->deviceHierarchyModel->getRootId($this->userId);
        }
        $nodeById = $this->deviceHierarchyModel->getAllNodesById($this->userId);
        $tree = $this->deviceHierarchyModel->getTree($this->userId, $nodeId);
        $devices = $this->flatTree($tree);
        $data = array();

        $maxLevels = array();
        $weather = array('temperature' => [], 'humidity' => [], 'pressure' => []);

        foreach ($devices as $device) {
            $sensors = $this->recordModel->getLastData($device['id']);
            $currentAvgType = '1';
            if (isset($_GET['avgType']) && $_GET['avgType'] == '24') {
                $currentAvgType = '24';
            }
            $averages = $this->recordModel->getAverages($device['id'], $currentAvgType);
            $path = \AirQualityInfo\Model\DeviceHierarchyModel::calculateDevicePath($nodeById, $device['id']);
            $data[] = array('sensors' => $sensors, 'averages' => $averages, 'device' => $device, 'breadcrumbs' => $path);

            $maxLevels[] = $averages['max_level'];
            foreach ($weather as $k => $_) {
                $weather[$k][] = $sensors[$k];
            }
        }

        $level = round(MainController::avg($maxLevels), 0);
        $this->render(array('view' => 'views/all_sensors.php'), array(
            'data' => $data,
            'currentAvgType' => $currentAvgType,
            'nodeId' => $nodeId,
            'displayCustomHeader' => true,
            'homeWidget' => array(
                'level' => $level,
                'locale' => ($level === null ? array() : $this->locale->getValue('_widgets')[$level]),
                'temperature' => MainController::median($weather['temperature']),
                'pressure' => MainController::median($weather['pressure']),
                'humidity' => MainController::median($weather['humidity']),
            )
        ));
    }

    private static function avg($arr) {
        $arr = array_filter($arr, function($v) { return $v !== null; } );
        if (count($arr) === 0) {
            return null;
        }
        return array_sum($arr) / count($arr);
    }

    private static function median($arr) {
        $arr = array_filter($arr, function($v) { return $v !== null; } );
        $c = count($arr);
        if ($c === 0) {
            return null;
        }
        sort($arr);
        if (($c % 2) == 1) {
            return $arr[floor($c / 2)];
        } else {
            return ($arr[$c / 2 - 1] + $arr[$c / 2]) / 2;
        }
    }
}

?>