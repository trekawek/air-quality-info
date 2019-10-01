<?php
namespace AirQualityInfo\Controller;

class AbstractController {

    private $templateVariables;

    private $attachmentModel;

    private $templateModel;

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

        $domainTemplate = $this->templateModel->getTemplate($this->userId);
        if ($this->attachmentModel->getFileInfo($this->userId, 'brand_icon')) {
            $customBrandIcon = l('attachment', 'get', null, array('name' => 'brand_icon'));
        }

        if ($args['layout']) {
            include('partials/head.php');
        }

        include($args['view']);

        if ($args['layout']) {
            include('partials/tail.php');
        }
    }

    protected function flatTree($tree) {
        return \AirQualityInfo\Model\DeviceHierarchyModel::flatTree($tree, $this->deviceById);
    }

    // @Inject
    public function setTemplateVariables($templateVariables) {
        $this->templateVariables = $templateVariables;
    }

    public function setDeviceHierarchyModel(\AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel) {
        $this->deviceHierarchyModel = $deviceHierarchyModel;
    }

    public function setAttachmentModel(\AirQualityInfo\Model\AttachmentModel $attachmentModel) {
        $this->attachmentModel = $attachmentModel;
    }

    public function setTemplateModel(\AirQualityInfo\Model\TemplateModel $templateModel) {
        $this->templateModel = $templateModel;
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
        } else if (isset($node['children'])) {
            foreach ($node['children'] as $c) {
                if ($this->isDisplayMap($c)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function getUriPrefix($userDomain) {
        $uri_prefix = '//' . $userDomain . CONFIG['user_domain_suffixes'][0];
        $host = explode(':', $_SERVER['HTTP_HOST']);
        if (isset($host[1])) {
            $port = $host[1];
        }
        if (isset($port) && $port != 80 && $port != 443) {
            $uri_prefix .= ":$port";
        }
        return $uri_prefix;
    }
}
?>