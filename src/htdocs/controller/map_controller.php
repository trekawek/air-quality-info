<?php
namespace AirQualityInfo\Controller;

class MapController extends AbstractController {

    private $recordModel;

    public function __construct(
            \AirQualityInfo\Model\RecordModel $recordModel) {
        $this->recordModel = $recordModel;
    }

    public function index() {
        $this->render(array('view' => 'views/map/index.php'));
    }

    public function data() {
        $nodeId = $this->deviceHierarchyModel->getRootId($this->userId);
        $tree = $this->deviceHierarchyModel->getTree($this->userId, $nodeId);
        $devices = $this->flatTree($tree);
        $deviceFields = array('name', 'description', 'path');
        foreach ($devices as $device) {
            $deviceFiltered = array();
            foreach ($deviceFields as $f) {
                $deviceFiltered[$f] = $device[$f];
            }
            if ($device['location_provided']) {
                $deviceFiltered['lat'] = $device['lat'];
                $deviceFiltered['lng'] = $device['lng'];
            }
            $deviceFiltered['data'] = $this->recordModel->getLastData($device['id']);
            $deviceFiltered['averages'] = $this->recordModel->getAverages($device['id'], 1);
            $deviceFiltered['info_path'] = l('map', 'sensorInfo', $device);
            $data[] = $deviceFiltered;
        }
        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function sensorInfo($device) {
        $lastData = $this->recordModel->getLastData($device['id']);
        $averages = $this->recordModel->getAverages($device['id'], 1);
        $this->render(array('view' => 'views/map/sensor_info.php', 'layout' => false), array(
            'averages' => $averages,
            'currentAvgType' => 1,
            'sensors' => $lastData,
            'device' => $device
        ));
    }

}
?>