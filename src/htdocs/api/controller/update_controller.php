<?php
namespace AirQualityInfo\Api\Controller;

class UpdateController {

    private $userModel;

    private $deviceModel;

    private $jobUtils;

    public function __construct(
            \AirQualityInfo\Model\UserModel $userModel,
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Lib\JobUtils $jobUtils) {
        $this->userModel = $userModel;
        $this->deviceModel = $deviceModel;
        $this->jobUtils = $jobUtils;
    }

    public function updateWithKey($key) {
        $device = $this->deviceModel->getDeviceByApiKey($key);
        if (!$device) {
            $this->authError();
        }
        $payload = file_get_contents("php://input");
        $this->update($device, $payload);
    }

    public function update($device, $payload, $data = null) {
        $now = time();
        if ($device['last_update'] !== null && $now - $device['last_update'] < 90) {
            http_response_code(429);
            die();
        }
        $data = json_decode($payload, true);
        if (isset($data['esp8266id']) && $data['esp8266id'] != $device['esp8266_id']) {
            $this->deviceModel->updateDevice($device['id'], array('esp8266_id' => $data['esp8266id']));
        }
        if ($device['id'] === null) {
            throw new \Exception("Device can't be null");
        }
        $this->deviceModel->updateDevice($device['id'], array('last_update' => $now));
        $this->jobUtils->createJob('update', 'update', array($device['id'], $now, $payload));
    }

    public function updateTtn($ttnApiKey) {
        $now = time();
        $userId = $this->userModel->getIdByTtnApiKey($ttnApiKey);
        if ($userId === null) {
            http_response_code(403);
            die('Forbidden');
        }

        $payload = file_get_contents("php://input");
        $data = json_decode($payload, true);

        if (!isset($data['end_device_ids']['device_id'])) {
            http_response_code(403);
            die('Forbidden');
        }

        $device = $this->deviceModel->getDeviceByUserAndTtnId($userId, $data['end_device_ids']['device_id']);
        if ($device === false) {
            http_response_code(403);
            die('Forbidden');
        }
        $this->deviceModel->updateDevice($device['id'], array('last_update' => $now));
        $this->jobUtils->createJob('update', 'update', array($device['id'], $now, $payload));
    }

    private function authError() {
        header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
        header('HTTP/1.0 401 Unauthorized');
        die();
    }
}

?>