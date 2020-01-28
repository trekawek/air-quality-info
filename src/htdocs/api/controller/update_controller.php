<?php
namespace AirQualityInfo\Api\Controller;

class UpdateController {

    private $updater;

    private $jsonUpdateModel;

    private $deviceModel;

    public function __construct(
            \AirQualityInfo\Model\Updater $updater,
            \AirQualityInfo\Model\JsonUpdateModel $jsonUpdateModel,
            \AirQualityInfo\Model\DeviceModel $deviceModel) {
        $this->updater = $updater;
        $this->jsonUpdateModel = $jsonUpdateModel;
        $this->deviceModel = $deviceModel;
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
        if ($device['last_update'] !== null && $now - $device['last_update'] < 120) {
            http_response_code(429);
            die();
        }

        if ($data === null) {
            $data = json_decode($payload, true);
        }
        $device['mapping'] = $this->deviceModel->getMappingAsAMap($device['id']);

        $sensors = $data['sensordatavalues'];
        $map = array();
        foreach ($sensors as $row) {
            $map[$row['value_type']] = $row['value'];
        }
        
        $this->jsonUpdateModel->logJsonUpdate($device['id'], time(), $payload);
        $this->updater->update($device, $map);
        $this->deviceModel->updateDevice($device['id'], array('last_update' => $now));
    }

    private function authError() {
        header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
        header('HTTP/1.0 401 Unauthorized');
        die();
    }
}

?>