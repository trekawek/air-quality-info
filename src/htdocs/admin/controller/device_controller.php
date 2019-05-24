<?php
namespace AirQualityInfo\Admin\Controller;

class DeviceController extends AbstractController {

    private $deviceModel;

    private $recordModel;

    private $jsonUpdateModel;

    private $madaviMigrator;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\JsonUpdateModel $jsonUpdateModel,
            \AirQualityInfo\Model\Migration\MadaviMigrator $madaviMigrator) {
        $this->deviceModel = $deviceModel;
        $this->recordModel = $recordModel;
        $this->jsonUpdateModel = $jsonUpdateModel;
        $this->madaviMigrator = $madaviMigrator;
        $this->title = __('Devices');
    }

    public function index() {
        $devices = $this->deviceModel->getDevicesForUser($this->user['id']);
        $this->render(array('view' => 'admin/views/device/index.php'), array('devices' => $devices));
    }

    public function create() {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $deviceForm->addElement('esp8266_id', 'number', 'ESP 8266 id')->addRule('required')->addRule('numeric');
        $deviceForm->addElement('name', 'text', 'Name')->addRule('required');
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        $deviceForm->addElement('hidden', 'checkbox', 'Hidden');
        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $id = $this->deviceModel->createDevice(array(
                'user_id' => $this->user['id'],
                'esp8266_id' => $_POST['esp8266_id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'hidden' => isset($_POST['hidden']) ? 1 : 0,
                'http_username' => $this->user['email'],
                'http_password' => bin2hex(random_bytes(16))
            ));
            $this->alert(__('Created a new device', 'success'));
            header('Location: '.l('device', 'edit', null, array('device_id' => $id)));
        } else {
            $this->render(array(
                'view' => 'admin/views/device/create.php'
            ), array(
                'deviceForm' => $deviceForm
            ));
        }
    }

    public function edit($deviceId) {
        $device = $this->getDevice($deviceId);

        $deviceForm = $this->getDeviceForm();
        $deviceForm->setDefaultValues($device);

        $mappingForm = $this->getMappingForm($deviceId);

        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $data = array(
                'name' => $_POST['name'],
                'description' => $_POST['description']
            );
            $this->deviceModel->updateDevice($deviceId, $data);
            $this->alert(__('Updated the device', 'success'));
        }

        if ($mappingForm->isSubmitted() && $mappingForm->validate($_POST)) {
            $this->deviceModel->addMapping($deviceId, $_POST['db_name'], $_POST['json_name']);
            $this->alert(__('Created a new mapping', 'success'));    
        }

        $mapping = $this->deviceModel->getMappingForDevice($deviceId);

        $this->render(array(
            'view' => 'admin/views/device/edit.php'
        ), array(
            'deviceId' => $deviceId,
            'deviceForm' => $deviceForm,
            'mappingForm' => $mappingForm,
            'mapping' => $mapping,
            'lastRecord' => $this->recordModel->getLastData($deviceId),
            'jsonUpdates' => $this->jsonUpdateModel->getJsonUpdates($deviceId, 5),
        ));
    }

    public function move($deviceId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->move($deviceId, $_POST['move']);
        return $this->index();
    }

    public function deleteDevice($deviceId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->deleteDevice($deviceId);
        $this->alert(__('Deleted the device'));
    }

    public function deleteMapping($deviceId, $mappingId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->deleteMapping($deviceId, $mappingId);
        $this->alert(__('Deleted the mapping'));
    }

    public function importMadavi($deviceId) {
        $device = $this->getDevice($deviceId);
        DeviceController::chunkedContent();
        $this->madaviMigrator->migrate($device);
        echo "Madavi records has been imported";
    }

    public function resetHttpPassword($deviceId) {
        $device = $this->getDevice($deviceId);
        $data = array(
            'http_password' => bin2hex(random_bytes(16))
        );
        $this->deviceModel->updateDevice($deviceId, $data);
        $this->alert(__('New password has been set.', 'success'));
        header('Location: '.l('device', 'edit', null, array('device_id' => $deviceId)));
    }

    private function getDeviceForm() {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $deviceForm->addElement('esp8266_id', 'text', 'ESP 8266 id', array('disabled' => true));
        $deviceForm->addElement('name', 'text', 'Name')->addRule('required');
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        $deviceForm->addElement('hidden', 'checkbox', 'Hidden');
        return $deviceForm;
    }

    private function getMappingForm($deviceId) {
        $options = array_keys(\AirQualityInfo\Model\Updater::VALUE_MAPPING);
        $options = array_combine($options, $options);

        $mappingForm = new \AirQualityInfo\Lib\Form\Form("mappingForm");
        $mappingForm->addElement('json_name', 'text', 'JSON field')->addRule('required');
        $mappingForm->addElement('db_name', 'select', 'Database field')
            ->addRule('required')
            ->setOptions($options);
        return $mappingForm;
    }

    private function getDevice($deviceId) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        if ($device == null || $device['user_id'] != $this->user['id']) {
            header('Location: '.l('device', 'index'));
            die();
        }
        return $device;
    }

    private static function chunkedContent() {
        set_time_limit(60 * 60);
        header('Content-Type: text/event-stream');
        header('X-Accel-Buffering: no');
        flush();
        ob_end_flush();
    }
}

?>