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
            'pm10' => $_REQUEST['pm10'],
            'pm25' => $_REQUEST['pm2_5']
        ));

        echo 'Update accepted';
    }
}

$controllers['update'] = new UpdateEspEasyController($dao, $updater);
$routes['GET /:device/update/:id'] = array('update', 'update');

?>