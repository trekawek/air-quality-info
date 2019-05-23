<?php
namespace AirQualityInfo\Admin\Controller;

class DeviceJsonController extends AbstractController {

    private $deviceModel;

    private $recordModel;

    private $jsonUpdateModel;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\JsonUpdateModel $jsonUpdateModel) {
        $this->deviceModel = $deviceModel;
        $this->jsonUpdateModel = $jsonUpdateModel;
        $this->title = __('JSONs');
    }

    public function get($deviceId, $timestamp) {
        $this->authorizeDevice($deviceId);
        $json = $this->jsonUpdateModel->getJsonUpdate($deviceId, $timestamp);
        header('Content-type: application/json');
        echo $json;
    }

    public function index($deviceId) {
        $this->authorizeDevice($deviceId);
        $this->render(array(
            'view' => 'admin/views/device_json/index.php'
        ), array(
            'deviceId' => $deviceId,
            'jsonUpdates' => $this->jsonUpdateModel->getJsonUpdates($deviceId),
        ));
    }

    private function authorizeDevice($deviceId) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        if ($device == null || $device['user_id'] != $this->user['id']) {
            header('Location: '.l('device', 'index'));
            die();
        }
    }
}
?>