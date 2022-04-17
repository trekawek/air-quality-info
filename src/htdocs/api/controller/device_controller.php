<?php
namespace AirQualityInfo\Api\Controller;

class DeviceController {

    private $deviceModel;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel) {
        $this->deviceModel = $deviceModel;
    }

    public function register($esp8266id) {
        $apiToken = bin2hex(random_bytes(16));
        $assignToken = bin2hex(random_bytes(16));

        $this->deviceModel->createDevice(array(
            'user_id' => 9999,
            'name' => 'nam-'.$esp8266id,
            'description' => 'Nettigo Air Monitor '.$esp8266id,
            'update_mode' => 'push',
            'api_key' => $apiToken,
            'assign_token' => $assignToken,
            'default_device' => 0,
            'location_provided' => 0
        ));

        header('Content-type: application/json');
        echo json_encode(array(
            'api_token' => $apiToken,
            'assign_token' => $assignToken,

        ));
    }

    public function isAssigned($key) {
        $deviceId = $this->deviceModel->getDeviceByAssignToken($key);
        $assigned = $deviceId !== false && $deviceId['user_id'] != 9999;
        header('Content-type: application/json');
        echo json_encode($assigned);
    }

}

?>