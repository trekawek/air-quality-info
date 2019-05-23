<?php
namespace AirQualityInfo\Controller;

class UpdateController extends AbstractController {

    private $updater;

    private $jsonUpdateModel;

    private $deviceModel;

    private $devices;

    public function __construct(
            \AirQualityInfo\Model\Updater $updater,
            \AirQualityInfo\Model\JsonUpdateModel $jsonUpdateModel,
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            $devices) {
        $this->updater = $updater;
        $this->jsonUpdateModel = $jsonUpdateModel;
        $this->deviceModel = $deviceModel;
        $this->devices = $devices;
    }

    public function update() {
        $matchingDevices = $this->authWithHttpBasic();
        if ($matchingDevices === null) {
            $this->authError();
        }

        $payload = file_get_contents("php://input");
        $data = json_decode($payload, true);
        $device = $this->authWithEsp8266id($matchingDevices, $data);
        if ($device === null) {
            $this->authError();
        }
        $mapping = array();
        foreach ($this->deviceModel->getMappingForDevice($device['id']) as $m) {
            if (!isset($mapping[$m['db_name']])) {
                $mapping[$m['db_name']] = array();
            }
            $mapping[$m['db_name']][] = $mapping[$m['json_name']];
        }
        $device['mapping'] = $mapping;

        $sensors = $data['sensordatavalues'];
        $map = array();
        foreach ($sensors as $row) {
            $map[$row['value_type']] = $row['value'];
        }
        
        $this->jsonUpdateModel->logJsonUpdate($device['id'], time(), $payload);
        $this->updater->update($device, $map);
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

    private function authWithEsp8266id($devices, $data) {
        foreach ($devices as $d) {
            if ($d['esp8266_id'] == $data['esp8266id']) {
                return $d;
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