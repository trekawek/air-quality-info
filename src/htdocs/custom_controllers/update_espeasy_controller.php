<?php
class UpdateEspEasyController extends AbstractController {

    private $updater;

    public function __construct($dao, $updater) {
        parent::__construct();
        $this->updater = $updater;
        $this->dao = $dao;
    }

    public function update($device, $uuid) {        
        if ($device['uuid'] != $uuid) {
            error_log('uuid mismatch. Expected: '.$device['uuid'].' but got '.$uuid);
            exit;
        }
    
        $this->updater->update($device, array(
            'PMS_P0' => $_REQUEST['pm1_0'],
            'PMS_P1' => $_REQUEST['pm10'],
            'PMS_P2' => $_REQUEST['pm2_5']
        ));

        echo 'Update accepted';
    }
}

$controllers['update_esp'] = new UpdateEspEasyController($dao, $updater);
$routes['GET /:device/update/:uuid'] = array('update_esp', 'update');

?>