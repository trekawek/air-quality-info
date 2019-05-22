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

    public function edit($deviceId) {
        $device = $this->getDevice($deviceId);

        $form = new \AirQualityInfo\Lib\Form\Form();
        $form->addElement('esp8266_id', 'text', 'ESP 8266 id', array('disabled' => true));
        $form->addElement('name', 'text', 'Name')->addRule('required');
        $form->addElement('description', 'text', 'Description')->addRule('required');
        $form->addElement('http_username', 'text', 'HTTP username', array('disabled' => true));
        $form->addElement('http_password', 'text', 'HTTP password', array('disabled' => true));
        $form->addElement('hidden', 'checkbox', 'Hidden');
        $form->setDefaultValues($device);

        if ($form->isSubmitted() && $form->validate($_POST)) {
            $data = array(
                'name' => $_POST['name'],
                'description' => $_POST['description']
            );
            $this->deviceModel->updateDevice($deviceId, $data);
            header('Location: '.l('device', 'index'));
        } else {
            $this->render(array(
                'view' => 'admin/views/devices/edit.php'
            ), array(
                'deviceId' => $deviceId,
                'form' => $form
            ));
        }
    }

    private function getDevice($deviceId) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        if ($device == null || $device['user_id'] != $this->user['id']) {
            header('Location: '.l('device', 'index'));
            die();
        }
        return $device;
    }
}

?>