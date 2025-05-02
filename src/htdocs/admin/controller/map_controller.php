<?php
namespace AirQualityInfo\Admin\Controller;

class MapController extends AbstractController {

    private $recordModel;

    private $deviceModel;

    private $deviceHierarchyModel;

    public function __construct(
        \AirQualityInfo\Model\RecordModel $recordModel,
        \AirQualityInfo\Model\DeviceModel $deviceModel,
        \AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel) {
        $this->recordModel = $recordModel;
        $this->deviceModel = $deviceModel;
        $this->deviceHierarchyModel = $deviceHierarchyModel;
        $this->authorizationRequired = false;
    }

    public function index() {
        $this->render(array(
            'view' => 'admin/views/map_osm.php',
            'head' => 'admin/partials/about/head.php',
            'tail' => 'admin/partials/about/tail.php'
        ));
    }

    public function data() {
        $data = $this->cache->load('/map/data.json');
        if ($data === null) {
            $data = $this->loadData();
            $this->cache->save('/map/data.json', $data, array(
                \Nette\Caching\Cache::EXPIRE => '5 minutes',
            ));
        }
        header('Content-type: application/json');
        echo json_encode($data);
    }

    private function loadData() {
        $data = array();
        foreach ($this->userModel->getAllUsers() as $user) {
            $userId = $user['id'];
            $urlPrefix = $this->getUriPrefix($user['domain']);

            $deviceById = array();
            $nodeById = array();
            foreach ($this->deviceModel->getDevicesForUser($userId) as $d) {
                $deviceById[$d['id']] = $d;
            }
            foreach ($this->deviceHierarchyModel->getAllNodes($userId) as $node) {
                $nodeById[$node['id']] = $node;
                if ($node['device_id']) {
                    $deviceId = $node['device_id'];
                    if (isset($deviceById[$deviceId])) {
                        $deviceById[$deviceId]['node_id'] = $node['id'];
                    }
                }
            }
            foreach ($deviceById as $i => $device) {
                if (!isset($device['node_id'])) {
                    unset($deviceById[$i]);
                    continue;
                }
                $path = \AirQualityInfo\Model\DeviceHierarchyModel::calculatePath($nodeById, $device['node_id']);
                $textPath = \AirQualityInfo\Model\DeviceHierarchyModel::getTextPath($path);
                $deviceById[$i]['path'] = $textPath;
            }
            $deviceFields = array('name', 'description');
            foreach ($deviceById as $device) {
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
                if ($deviceFiltered['averages']['values']['pm25'] === null) {
                    continue;
                }
                $deviceFiltered['path'] = $urlPrefix . $device['path'];
                $deviceFiltered['info_path'] = $urlPrefix . '/map' . $device['path'];
                $deviceFiltered['radius'] = intval($device['radius']);
                $data[] = $deviceFiltered;
            }
        }
        return $data;
    }
}
?>