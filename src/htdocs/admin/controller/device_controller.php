<?php
namespace AirQualityInfo\Admin\Controller;

class DeviceController extends AbstractController {

    private $deviceModel;

    private $deviceHierarchyModel;

    private $recordModel;

    private $jsonUpdateModel;

    private $madaviMigrator;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel,
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\JsonUpdateModel $jsonUpdateModel,
            \AirQualityInfo\Model\Migration\MadaviMigrator $madaviMigrator) {
        $this->deviceModel = $deviceModel;
        $this->deviceHierarchyModel = $deviceHierarchyModel;
        $this->recordModel = $recordModel;
        $this->jsonUpdateModel = $jsonUpdateModel;
        $this->madaviMigrator = $madaviMigrator;
        $this->title = __('Devices');
    }

    public function index() {
        $devices = $this->deviceModel->getDevicesForUser($this->user['id']);
        foreach ($devices as $i => $d) {
            $paths = $this->deviceHierarchyModel->getDevicePaths($this->user['id'], $d['id']);
            $path = null;
            if (!empty($paths)) {
                $path = $paths[0];
            }
            $devices[$i]['path'] = $path;
        }
        $this->render(array('view' => 'admin/views/device/index.php'), array('devices' => $devices));
    }

    public function create() {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $deviceForm->addElement('esp8266_id', 'number', 'ESP 8266 id')->addRule('required')->addRule('numeric');
        $this->addNameField($deviceForm)
            ->setOptions(array('prepend' => 'https://' . $this->user['domain'] . CONFIG['user_domain_suffixes'][0] . '/'));
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $deviceId = $this->deviceModel->createDevice(array(
                'user_id' => $this->user['id'],
                'esp8266_id' => $_POST['esp8266_id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'http_username' => $this->user['email'],
                'http_password' => bin2hex(random_bytes(16))
            ));

            $rootId = $this->deviceHierarchyModel->getRootId($this->user['id']);
            $this->deviceHierarchyModel->addChild($this->user['id'], $rootId, null, null, $deviceId);
            
            $this->alert(__('Created a new device', 'success'));
            header('Location: '.l('device', 'edit', null, array('device_id' => $deviceId)));
        } else {
            $this->render(array(
                'view' => 'admin/views/device/create.php'
            ), array(
                'deviceForm' => $deviceForm
            ));
        }
    }

    public function edit($deviceId) {
        $device = $this->getDevice($deviceId);
        $nodes = $this->deviceHierarchyModel->getDeviceNodes($this->user['id'], $device['id']);
        $breadcrumbs = null;
        if (!empty($nodes)) {
            $breadcrumbs = $this->deviceHierarchyModel->getPath($this->user['id'], $nodes[0]);
        }

        $deviceForm = $this->getDeviceForm();
        $deviceForm->setDefaultValues($device);

        $mappingForm = $this->getMappingForm($deviceId);

        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $data = array(
                'name' => $_POST['name'],
                'description' => $_POST['description']
            );
            $this->deviceModel->updateDevice($deviceId, $data);
            $this->alert(__('Updated the device', 'success'));
        }

        if ($mappingForm->isSubmitted() && $mappingForm->validate($_POST)) {
            $this->deviceModel->addMapping($deviceId, $_POST['db_name'], $_POST['json_name']);
            $this->alert(__('Created a new mapping', 'success'));    
        }

        $mapping = $this->deviceModel->getMappingForDevice($deviceId);

        $this->render(array(
            'view' => 'admin/views/device/edit.php'
        ), array(
            'device' => $device,
            'deviceId' => $deviceId,
            'deviceForm' => $deviceForm,
            'mappingForm' => $mappingForm,
            'mapping' => $mapping,
            'lastRecord' => $this->recordModel->getLastData($deviceId),
            'jsonUpdates' => $this->jsonUpdateModel->getJsonUpdates($deviceId, 5),
            'breadcrumbs' => $breadcrumbs,
            'lastItemLink' => false
        ));
    }

    public function makeDefault($deviceId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->makeDefault($this->user['id'], $deviceId);
        $this->alert(__('Updated the default device'));
        header('Location: '.l('device', 'edit', null, array('device_id' => $deviceId)));
    }

    public function deleteDevice($deviceId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->deleteDevice($deviceId);
        $this->alert(__('Deleted the device'));
    }

    public function deleteMapping($deviceId, $mappingId) {
        $this->getDevice($deviceId); // validate the device ownership
        $this->deviceModel->deleteMapping($deviceId, $mappingId);
        $this->alert(__('Deleted the mapping'));
    }

    public function importMadaviWrapper($deviceId) {
        $device = $this->getDevice($deviceId);
        $this->render(array(
            'view' => 'admin/views/device/import_madavi.php'
        ), array(
            'post' => l('device', 'importMadavi', null, array('device_id' => $deviceId)),
            'deviceId' => $deviceId
        ));
    }

    public function importMadavi($deviceId) {
        $device = $this->getDevice($deviceId);
        DeviceController::chunkedContent();
        $this->madaviMigrator->migrate($device);
        echo "Madavi records has been imported";
    }

    public function resetHttpPassword($deviceId) {
        $device = $this->getDevice($deviceId);
        $data = array(
            'http_password' => bin2hex(random_bytes(16))
        );
        $this->deviceModel->updateDevice($deviceId, $data);
        $this->alert(__('New password has been set.', 'success'));
        header('Location: '.l('device', 'edit', null, array('device_id' => $deviceId)));
    }

    private function getDeviceForm() {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $deviceForm->addElement('esp8266_id', 'text', 'ESP 8266 id', array('disabled' => true));
        $this->addNameField($deviceForm);
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        return $deviceForm;
    }

    private function getMappingForm($deviceId) {
        $options = array_keys(\AirQualityInfo\Model\Updater::VALUE_MAPPING);
        $options = array_combine($options, $options);

        $mappingForm = new \AirQualityInfo\Lib\Form\Form("mappingForm");
        $mappingForm->addElement('json_name', 'text', 'JSON field')->addRule('required');
        $mappingForm->addElement('db_name', 'select', 'Database field')
            ->addRule('required')
            ->setOptions($options);
        return $mappingForm;
    }

    private function getDevice($deviceId) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        if ($device == null || $device['user_id'] != $this->user['id']) {
            header('Location: '.l('device', 'index'));
            die();
        }
        return $device;
    }

    private static function chunkedContent() {
        set_time_limit(60 * 60);
        header('Content-Type: text/event-stream; charset=utf-8');
        header('X-Accel-Buffering: no');
        flush();
        ob_end_flush();
    }

    private function addNameField($deviceForm) {
        return $deviceForm->addElement('name', 'text', 'Name')
            ->addRule('required')
            ->addRule('regexp', array('pattern' => '/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', 'message' => __('The name should consist of alphanumeric characters and dashes')));
    }
}

?>