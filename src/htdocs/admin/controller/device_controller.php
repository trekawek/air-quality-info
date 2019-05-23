<?php
namespace AirQualityInfo\Admin\Controller;

class DeviceController extends AbstractController {

    private $deviceModel;

    public function __construct(\AirQualityInfo\Model\DeviceModel $deviceModel) {
        $this->deviceModel = $deviceModel;
        $this->title = __('Devices');
    }

    public function index() {
        $devices = $this->deviceModel->getDevicesForUser($this->user['id']);
        $this->render(array('view' => 'admin/views/devices/index.php'), array('devices' => $devices));
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
            header('Location: '.l('device', 'edit', null, array('device_id' => $id)));
        } else {
            $this->render(array(
                'view' => 'admin/views/devices/create.php'
            ), array(
                'deviceForm' => $deviceForm
            ));
        }
    }

    public function edit($deviceId) {
        $device = $this->getDevice($deviceId);
        $mapping = $this->deviceModel->getMappingForDevice($deviceId);

        $deviceForm = $this->getDeviceForm();
        $deviceForm->setDefaultValues($device);

        $this->render(array(
            'view' => 'admin/views/devices/edit.php'
        ), array(
            'deviceId' => $deviceId,
            'deviceForm' => $deviceForm,
            'mappingForm' => $this->getMappingForm($deviceId),
            'mapping' => $mapping
        ));
    }

    public function deleteDevice($deviceId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->deleteDevice($deviceId);
    }

    public function update($deviceId) {
        $device = $this->getDevice($deviceId);
        $deviceForm = $this->getDeviceForm();
        $deviceForm->validate($_POST);
        $data = array(
            'name' => $_POST['name'],
            'description' => $_POST['description']
        );
        $this->deviceModel->updateDevice($deviceId, $data);
        return $this->edit($deviceId);
    }

    public function createMapping($deviceId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->addMapping($deviceId, $_POST['db_name'], $_POST['json_name']);
        header('Location: '.l('device', 'edit', null, array('device_id' => $deviceId)));
    }

    public function deleteMapping($deviceId, $mappingId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->deleteMapping($deviceId, $mappingId);
    }

    private function getDevice($deviceId) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        if ($device == null || $device['user_id'] != $this->user['id']) {
            header('Location: '.l('device', 'index'));
            die();
        }
        return $device;
    }

    private function getDeviceForm() {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $deviceForm->addElement('esp8266_id', 'text', 'ESP 8266 id', array('disabled' => true));
        $deviceForm->addElement('name', 'text', 'Name')->addRule('required');
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        $deviceForm->addElement('http_username', 'text', 'HTTP username', array('disabled' => true));
        $deviceForm->addElement('http_password', 'text', 'HTTP password', array('disabled' => true));
        $deviceForm->addElement('hidden', 'checkbox', 'Hidden');
        return $deviceForm;
    }

    private function getMappingForm($deviceId) {
        $options = array_keys(\AirQualityInfo\Model\Updater::VALUE_MAPPING);
        $options = array_combine($options, $options);

        $mappingForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $mappingForm->addElement('json_name', 'text', 'JSON field')->addRule('required');
        $mappingForm->addElement('db_name', 'select', 'Database field')
            ->addRule('required')
            ->setOptions($options);
        return $mappingForm;
    }
}

?>