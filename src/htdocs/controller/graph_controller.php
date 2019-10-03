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
            $graphs[] = 'pm';
        }
        if ($sensors['temperature'] !== null) {
            $graphs[] = 'temperature';
        }
        if ($sensors['humidity'] !== null) {
            $graphs[] = 'humidity';
        }
        if ($sensors['pressure'] !== null) {
            $graphs[] = 'pressure';
        }

        $nodeById = $this->deviceHierarchyModel->getAllNodesById($this->userId);
        $path = \AirQualityInfo\Model\DeviceHierarchyModel::calculateDevicePath($nodeById, $device['id']);

        $this->render(array('view' => 'views/graphs.php'), array(
            'graphs' => $graphs,
            'device' => $device,
            'breadcrumbs' => $path
        ));
    }

    public function get_data($device) {
        $data = $this->recordModel->getHistoricData(
            $device['id'],
            isset($_GET['type']) ? $_GET['type'] : 'pm',
            isset($_GET['range']) ? $_GET['range'] : 'day',
            isset($_GET['ma_h']) ? $_GET['ma_h'] : null
        );
        if ($data === null) {
            http_response_code(404);
            die();
        }
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($data);
    }
}

?>