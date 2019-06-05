<?php
namespace AirQualityInfo\Controller;

class AbstractController {

    private $templateVariables;

    protected $deviceHierarchyModel;

    protected $userId;

    protected $deviceById;

    public function render($args, $data = array()) {
        $args = array_merge(array(
            'layout' => true
        ), $args);

        $deviceTree = $this->deviceHierarchyModel->getTree($this->userId);
        $displayLocations = $this->isDisplayLocations($deviceTree);
        $displayMap = $this->isDisplayMap($deviceTree);
        $this->addDevices($deviceTree);

        extract($this->templateVariables);
        extract($data);

        if ($args['layout']) {
            include('partials/head.php');
        }

        include($args['view']);

        if ($args['layout']) {
            include('partials/tail.php');
        }
    }

    protected function flatTree($tree) {
        $devices = array();
        if ($tree['device_id']) {
            $devices[] = $this->deviceById[$tree['device_id']];
        } else if (isset($tree['children'])) {
            foreach ($tree['children'] as $c) {
                $devices = array_merge($devices, $this->flatTree($c));
            }
        }
        return $devices;
    }

    // @Inject
    public function setTemplateVariables($templateVariables) {
        $this->templateVariables = $templateVariables;
    }

    public function setDeviceHierarchyModel(\AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel) {
        $this->deviceHierarchyModel = $deviceHierarchyModel;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setDevices($devices) {
        $this->deviceById = array();
        foreach ($devices as $d) {
            $this->deviceById[$d['id']] = $d;
        }
    }

    private function addDevices(&$node) {
        if ($node['device_id']) {
            $node['device'] = $this->deviceById[$node['device_id']];
        } else if (isset($node['children'])) {
            foreach ($node['children'] as $i => $n) {
                $this->addDevices($n);
                $node['children'][$i] = $n;
            }
        }
    }

    private function isDisplayLocations($deviceTree) {
        $children = $deviceTree['children'];
        $count = count($children);
        if ($count == 1) {
            return $children[0]['device_id'] === null;
        } else if ($count > 1) {
            return true;
        } else {
            return false;
        }
    }

    private function isDisplayMap($node) {
        if ($node['location_provided']) {
            return true;
        } else {
            foreach ($node['children'] as $c) {
                if ($this->isDisplayMap($c)) {
                    return true;
                }
            }
        }
        return false;
    }
}
?>