<?php
namespace AirQualityInfo\Controller;

class MapController extends AbstractController {

    private $recordModel;

    private $userModel;

    public function __construct(
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\UserModel $userModel) {
        $this->recordModel = $recordModel;
        $this->userModel = $userModel;
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
            if ($device['location_provided'] && $device['lat'] && $device['lng']) {
                $deviceFiltered['lat'] = $device['lat'];
                $deviceFiltered['lng'] = $device['lng'];
            } else {
                continue;
            }
            $deviceFiltered['data'] = $this->recordModel->getLastData($device['id']);
            $deviceFiltered['averages'] = $this->recordModel->getAverages($device['id'], 1);
            $deviceFiltered['info_path'] = l('map', 'sensorInfo', $device);
            $deviceFiltered['radius'] = intval($device['radius']);
            $data[] = $deviceFiltered;
        }
        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function sensorInfo($device) {
        $nodeById = $this->deviceHierarchyModel->getAllNodesById($this->userId);
        $path = \AirQualityInfo\Model\DeviceHierarchyModel::calculateDevicePath($nodeById, $device['id']);

        $lastData = $this->recordModel->getLastData($device['id']);
        $averages = $this->recordModel->getAverages($device['id'], 1);
        $user = $this->userModel->getUserById($this->userId);
        $domain = $user['domain'];
        $this->render(array('view' => 'views/map/sensor_info.php', 'layout' => false), array(
            'averages' => $averages,
            'currentAvgType' => 1,
            'sensors' => $lastData,
            'device' => $device,
            'deviceUrl' => $this->getUriPrefix($domain) . l('main', 'index', $device),
            'breadcrumbs' => $path
        ));
    }

}
?>