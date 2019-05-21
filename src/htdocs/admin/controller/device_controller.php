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
}

?>