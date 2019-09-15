<?php
namespace AirQualityInfo\Admin\Controller;

class DeviceHierarchyController extends AbstractController {

    private $deviceModel;

    private $deviceHierarchyModel;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel) {
        $this->deviceModel = $deviceModel;
        $this->deviceHierarchyModel = $deviceHierarchyModel;
        $this->title = __('Device hierarchy');
    }

    public function index($nodeId = null) {
        if ($nodeId === null) {
            $nodeId = $this->deviceHierarchyModel->getRootId($this->user['id']);
        }
        $breadcrumbs = $this->deviceHierarchyModel->getPath($this->user['id'], $nodeId);
        $nodes = $this->deviceHierarchyModel->getDirectChildren($this->user['id'], $nodeId);
        foreach ($nodes as $i => $node) {
            $path = $this->deviceHierarchyModel->getPath($this->user['id'], $node['id']);
            $node['path'] = $this->deviceHierarchyModel->getTextPath($path);
            $node['external'] = ($node['device_user_id'] != $this->user['id']);
            $nodes[$i] = $node;
        }
        $this->render(array(
            'view' => 'admin/views/device_hierarchy/index.php'
        ), array(
            'nodes' => $nodes,
            'nodeId' => $nodeId,
            'breadcrumbs' => $breadcrumbs,
            'lastItemLink' => false,
            'uriPrefix' => $this->getUriPrefix()
        ));
    }

    public function createDir($parentId) {
        $breadcrumbs = $this->deviceHierarchyModel->getPath($this->user['id'], $parentId);

        $nodeForm = new \AirQualityInfo\Lib\Form\Form("nodeForm");
        $this->addNameField($nodeForm, $breadcrumbs);
        $nodeForm->addElement('description', 'text', 'Description')->addRule('required');
        if ($nodeForm->isSubmitted() && $nodeForm->validate($_POST)) {
            $id = $this->deviceHierarchyModel->addChild(
                $this->user['id'],
                $parentId,
                $_POST['name'],
                $_POST['description']
            );
            $this->alert(__('Created a new directory', 'success'));
            header('Location: '.l('device_hierarchy', 'index', null, array('node_id' => $parentId)));
        } else {
            $this->render(array(
                'view' => 'admin/views/device_hierarchy/create_dir.php'
            ), array(
                'nodeForm' => $nodeForm,
                'parentId' => $parentId,
                'breadcrumbs' => $breadcrumbs,
                'lastItemLink' => true
            ));
        }
    }

    public function createDevice($parentId) {
        $breadcrumbs = $this->deviceHierarchyModel->getPath($this->user['id'], $parentId);
        $devices = array();
        foreach ($this->deviceModel->getDevicesForUser($this->user['id']) as $d) {
            $devices[$d['id']] = $d['description'];
        }
        $nodeForm = new \AirQualityInfo\Lib\Form\Form("nodeForm");
        $nodeForm->addElement('device_id', 'select', 'Device')
            ->addRule('required')
            ->setOptions($devices);
        if ($nodeForm->isSubmitted() && $nodeForm->validate($_POST)) {
            $id = $this->deviceHierarchyModel->addChild(
                $this->user['id'],
                $parentId,
                null,
                null,
                $_POST['device_id']
            );
            $this->alert(__('Linked device', 'success'));
            header('Location: '.l('device_hierarchy', 'index', null, array('node_id' => $parentId)));
        } else {
            $this->render(array(
                'view' => 'admin/views/device_hierarchy/create_device.php'
            ), array(
                'nodeForm' => $nodeForm,
                'parentId' => $parentId,
                'breadcrumbs' => $breadcrumbs,
                'lastItemLink' => true
            ));
        }
    }

    public function createExternalDevice($parentId) {
        $breadcrumbs = $this->deviceHierarchyModel->getPath($this->user['id'], $parentId);
        $nodeForm = new \AirQualityInfo\Lib\Form\Form("nodeForm");
        $nodeForm->addElement('uri', 'text', 'URI', array(), 'Please paste the URL address of the device you want to link. It must be a link within aqi.eco domain, eg.: https://smolna.aqi.eco/13b')
            ->addRule('required');
        if ($nodeForm->isSubmitted() && $nodeForm->validate($_POST)) {
            $deviceId = $this->getDeviceIdFromUrl($_POST['uri']);
            if ($deviceId != null) {
                $id = $this->deviceHierarchyModel->addChild(
                    $this->user['id'],
                    $parentId,
                    null,
                    null,
                    $deviceId
                );
                $this->alert(__('Linked device', 'success'));
                header('Location: '.l('device_hierarchy', 'index', null, array('node_id' => $parentId)));
                return;
            }
        }
        $this->render(array(
            'view' => 'admin/views/device_hierarchy/create_external_device.php'
        ), array(
            'nodeForm' => $nodeForm,
            'parentId' => $parentId,
            'breadcrumbs' => $breadcrumbs,
            'lastItemLink' => true
        ));
    }

    private function getDeviceIdFromUrl($uri) {
        $parsed = parse_url($uri);
        $host = $parsed['host'];
        $path = $parsed['path'];
        $userId = $this->userModel->parseFqdn($host);
        if ($userId == null) {
            $this->alert(__('Invalid domain', 'error'));
            return null;
        }
        $devices = $this->deviceModel->getDevicesForUser($userId);
        foreach ($devices as $i => $d) {
            $paths = $this->deviceHierarchyModel->getDevicePaths($userId, $d['id']);
            foreach ($paths as $p) {
                if ($path == $p) {
                    return $d['id'];
                }
            }
        }
        $this->alert(__('Invalid path', 'error'));
        return null;
    }

    public function editDirectory($nodeId) {
        $breadcrumbs = $this->deviceHierarchyModel->getPath($this->user['id'], $nodeId);
        $node = end($breadcrumbs);

        $nodeForm = new \AirQualityInfo\Lib\Form\Form("nodeForm");
        $this->addNameField($nodeForm, array_slice($breadcrumbs, 0, -1));
        $nodeForm->addElement('description', 'text', 'Description')->addRule('required');
        $nodeForm->setDefaultValues($node);

        if ($nodeForm->isSubmitted() && $nodeForm->validate($_POST)) {
            $id = $this->deviceHierarchyModel->updateNode(
                $this->user['id'],
                $nodeId,
                $_POST['name'],
                $_POST['description'],
                null
            );
            $this->alert(__('Updated directory', 'success'));
            header('Location: '.l('device_hierarchy', 'index', null, array('node_id' => $node['parent_id'])));
        } else {
            $this->render(array(
                'view' => 'admin/views/device_hierarchy/edit_dir.php'
            ), array(
                'nodeForm' => $nodeForm,
                'nodeId' => $nodeId,
                'breadcrumbs' => $breadcrumbs,
                'lastItemLink' => true
            ));
        }
    }

    public function move($nodeId) {
        $node = $this->deviceHierarchyModel->move($this->user['id'], $nodeId, $_POST['move']);
        header('Location: '.l('device_hierarchy', 'index', null, array('node_id' => $node['parent_id'])));
    }

    public function deleteNode($nodeId) {
        $this->deviceHierarchyModel->deleteNode($this->user['id'], $nodeId);
        $this->alert(__('Deleted the node'));
    }

    private function addNameField($deviceForm, $breadcrumbs) {

        $parentUrl = 'https://'
            .$this->user['domain']
            .CONFIG['user_domain_suffixes'][0]
            .\AirQualityInfo\Model\DeviceHierarchyModel::getTextPath($breadcrumbs)
            .'/';

        $deviceForm->addElement('name', 'text', 'Name')
            ->addRule('required')
            ->addRule('regexp', array('pattern' => '/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', 'message' => __('The name should consist of alphanumeric characters and dashes')))
            ->setOptions(array('prepend' => $parentUrl));
    }
}
?>