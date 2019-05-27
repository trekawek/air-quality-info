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
}
?>