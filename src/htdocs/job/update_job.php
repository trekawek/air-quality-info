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

        $map = array();
        if (isset($data['sensordatavalues'])) {
            $sensors = $data['sensordatavalues'];
            foreach ($sensors as $row) {
                $value = $row['value'];
                if ($value !== 'nan') {
                    $map[$row['value_type']] = $value;
                }
            }
        } else if (isset($data['uplink_message'])) {
            $map = $data['uplink_message']['decoded_payload'];
        }

        $this->jsonUpdateModel->logJsonUpdate($device['id'], $ts, $jsonPayload);
        $this->updater->update($device, $ts, $map);
    }
}
?>