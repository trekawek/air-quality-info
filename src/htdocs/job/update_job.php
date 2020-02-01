<?php
namespace AirQualityInfo\Job;

class UpdateJob {

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

    function update($deviceId, $ts, $jsonPayload) {
        $data = json_decode($jsonPayload, true);
        $device = $this->deviceModel->getDeviceById($deviceId);
        $device['mapping'] = $this->deviceModel->getMappingAsAMap($device['id']);

        $sensors = $data['sensordatavalues'];
        $map = array();
        foreach ($sensors as $row) {
            $map[$row['value_type']] = $row['value'];
        }
        $this->updater->update($device, $ts, $map);
        try {
            $this->jsonUpdateModel->logJsonUpdate($device['id'], $ts, $jsonPayload);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        } 
    }
}
?>