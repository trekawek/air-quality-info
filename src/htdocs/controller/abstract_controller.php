<?php
namespace AirQualityInfo\Controller;

class AbstractController {

    private $templateVariables;

    private $attachmentModel;

    private $templateModel;

    private $customFqdns;

    private $user;

    protected $deviceHierarchyModel;

    protected $userId;

    protected $deviceById;

    public function render($args, $data = array()) {
        $args = array_merge(array(
            'layout' => true,
            'head' => 'partials/head.php',
            'tail' => 'partials/tail.php',
        ), $args);

        $deviceTree = $this->deviceHierarchyModel->getTree($this->userId);
        $displayLocations = $this->isDisplayLocations($deviceTree);
        $displayMap = $this->isDisplayMap($deviceTree);
        $displayCustomHeader = false;
        $this->addDevices($deviceTree);

        extract($this->templateVariables);
        extract($data);

        $domainTemplate = $this->templateModel->getTemplate($this->userId);
        if ($this->attachmentModel->getFileInfo($this->userId, 'brand_icon')) {
            $customBrandIcon = l('attachment', 'get', null, array('name' => 'brand_icon'));
        }

        if ($args['layout']) {
            include($args['head']);
        }

        include($args['view']);

        if ($args['layout']) {
            include($args['tail']);
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

    public function setCustomFqdns($customFqdns) {
        $this->customFqdns = $customFqdns;
    }

    public function setUser($user) {
        $this->userId = $user['id'];
        $this->user = $user;
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

    protected function getUriPrefix() {
        if (empty($this->customFqdns)) {
            $uri_prefix = '//' . $this->user['domain'] . CONFIG['user_domain_suffixes'][0];
            $host = explode(':', $_SERVER['HTTP_HOST']);
            if (isset($host[1])) {
                $port = $host[1];
            }
            if (isset($port) && $port != 80 && $port != 443) {
                $uri_prefix .= ":$port";
            }
        } else {
            $uri_prefix = '//' . $this->customFqdns[0]['fqdn'];
        }
        return $uri_prefix;
    }
}
?>