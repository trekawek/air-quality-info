<?php
class DebugController extends AbstractController {

    private $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function index($device) {
        $sensors = $this->dao->getLastData($device['esp8266id']);
        $lastUpdate = $sensors['last_update'];
        unset($sensors['last_update']);
        $this->render(array('view' => 'views/debug.php'), array(
            'sensors' => $sensors,
            'lastUpdate' => $lastUpdate
        ));
    }

    public function index_json($device) {
        $this->authenticate($device);
        $jsonUpdates = $this->dao->getJsonUpdates($device['esp8266id']);
        $this->render(array('view' => 'views/debug_json.php'), array(
            'jsonUpdates' => $jsonUpdates
        ));
    }

    public function get_json($device, $timestamp) {
        $this->authenticate($device);
        $json = $this->dao->getJsonUpdate($device['esp8266id'], $timestamp);
        if ($json === null) {
          http_response_code(404);
          die();
        }
        header('Content-type:application/json;charset=utf-8');
        echo $json;        
    }
}

?>