<?php
class GraphController extends AbstractController {

    private $dao;

    public function __construct($dao) {
        parent::__construct();
        $this->dao = $dao;
    }

    public function index($device) {
        $sensors = $this->dao->getLastData($device['esp8266id']);
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
        $data = $this->dao->getHistoricData(
            $device['esp8266id'],
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