<?php
namespace AirQualityInfo\Api\Controller;

class UpdateController {

    private $deviceModel;

    private $jobUtils;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Lib\JobUtils $jobUtils) {
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