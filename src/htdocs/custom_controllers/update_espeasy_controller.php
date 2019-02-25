<?php
class UpdateEspEasyController extends AbstractController {

    private $updater;

    public function __construct($dao, $updater) {
        parent::__construct();
        $this->updater = $updater;
        $this->dao = $dao;
    }

    public function update($device, $uuid) {
        $payload = file_get_contents("php://input");
        error_log ("[update_raw] payload: [$payload] [$uuid]");
        error_log ("[update_raw] REQUEST: " . var_export($_REQUEST, true));
        error_log ("[update_raw] POST: " . var_export($_POST, true));
        error_log ("[update_raw] SERVER: " . var_export($_SERVER, true));
        echo "Data recieved";
    }
}

$controllers['update'] = new UpdateEspEasyController($dao, $updater);
$routes['GET /:device/update/:id'] = array('update', 'update');

?>