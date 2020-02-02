<?php
namespace AirQualityInfo\Controller;

class UpdateController extends AbstractController {

    private $updater;

    private $jsonUpdateModel;

    private $deviceModel;

    private $devices;

    private $apiUpdateController;

    public function __construct(
            \AirQualityInfo\Model\Updater $updater,
            \AirQualityInfo\Model\JsonUpdateModel $jsonUpdateModel,
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Api\Controller\UpdateController $apiUpdateController,
            $devices) {
        $this->updater = $updater;
        $this->jsonUpdateModel = $jsonUpdateModel;
        $this->deviceModel = $deviceModel;
        $this->devices = $devices;
        $this->apiUpdateController = $apiUpdateController;
    }

    public function update() {
        $matchingDevices = $this->authWithHttpBasic();
        if ($matchingDevices === null) {
            $this->authError();
        }
        $device = $matchingDevices[0];
        $payload = file_get_contents("php://input");
        $this->apiUpdateController->update($device, $payload);
    }

    public function updateWithKey($key) {
        $device = $this->authWithKey($key);
        if ($device === null) {
            $this->authError();
        }
        $payload = file_get_contents("php://input");
        $this->apiUpdateController->update($device, $payload);
    }

    private function authWithHttpBasic() {
        $matchingDevices = array();
        foreach ($this->devices as $device) {
            if ((isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $device['http_username'] && $_SERVER['PHP_AUTH_PW'] == $device['http_password'])) {
                $matchingDevices[] = $device;
            }
        }

        if (count($matchingDevices) > 0) {
            return $matchingDevices;
        } else {
            return null;
        }
    }

    private function authWithKey($key) {
        foreach ($this->devices as $device) {
            if ($key == $device['api_key']) {
                return $device;
            }
        }
        return null;
    }

    private function authError() {
        header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
        header('HTTP/1.0 401 Unauthorized');
        die();
    }
}

?>