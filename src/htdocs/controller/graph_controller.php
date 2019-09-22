<?php
namespace AirQualityInfo\Controller;

class GraphController extends AbstractController {

    private $recordModel;

    public function __construct(\AirQualityInfo\Model\RecordModel $recordModel) {
        $this->recordModel = $recordModel;
    }

    public function index($device) {
        $sensors = $this->recordModel->getLastData($device['id']);
        $graphs = array();
        if ($sensors['pm10'] !== null || $sensors['pm25'] !== null) {
            $graphs['pm'] = __('PM');
        }
        if ($sensors['temperature'] !== null) {
            $graphs['temperature'] = __('Temperature');
        }
        if ($sensors['humidity'] !== null) {
            $graphs['humidity'] = __('Humidity');
        }
        if ($sensors['pressure'] !== null) {
            $graphs['pressure'] = __('Pressure');
        }
        $i = 0;
        $this->render(array('view' => 'views/graphs.php'), array(
            'graphs' => $graphs
        ));
    }

    public function get_data($device) {
        $type = isset($_GET['type']) ? $_GET['type'] : 'pm';
        $range = isset($_GET['range']) ? $_GET['range'] : 'day';
        $ma_h = isset($_GET['ma_h']) ? $_GET['ma_h'] : null;

        $data = $this->recordModel->getHistoricData(
            $device['id'],
            $type,
            $range,
            $ma_h
        );
        if ($type == 'pm' && $range == 'day' && $ma_h == 1) {
            $instant = $this->recordModel->getHistoricData(
                $device['id'],
                $type,
                $range,
                null
            );
            $data['data']['pm10_instant'] = $instant['data']['pm10'];
            $data['data']['pm25_instant'] = $instant['data']['pm25'];
        }
        if ($data === null) {
            http_response_code(404);
            die();
        }
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($data);
    }
}

?>