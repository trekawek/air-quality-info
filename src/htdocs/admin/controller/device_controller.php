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
        $this->render(array('view' => 'admin/views/device/index.php'), array('devices' => $devices, 'uriPrefix' => $this->getUriPrefix()));
    }

    public function create() {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        if (isset($_GET['api_key']) || isset($_POST['api_key'])) {
            $deviceForm->addElement('api_key', 'text', 'API Key', array('readonly' => true))->addRule('required');
        }
        $this->addNameField($deviceForm)
            ->setOptions(array('prepend' => 'https://' . $this->user['domain'] . CONFIG['user_domain_suffixes'][0] . '/'));
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        $deviceForm->setDefaultValues(array(
            'api_key' => $_GET['api_key']
        ));
        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $deviceId = $this->deviceModel->createDevice(array(
                'user_id' => $this->user['id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'extra_description' => null,
                'update_mode' => 'push',
                'api_key' => isset($_POST['api_key']) ? $_POST['api_key'] : bin2hex(random_bytes(16)),
                'default_device' => 0,
                'location_provided' => 0
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
        if ($device['update_mode'] == 'pull') {
            header('Location: '.l('sensor', 'edit', null, array('device_id' => $deviceId)));
            return;
        }

        $nodes = $this->deviceHierarchyModel->getDeviceNodes($this->user['id'], $device['id']);
        $breadcrumbs = null;
        if (!empty($nodes)) {
            $breadcrumbs = $this->deviceHierarchyModel->getPath($this->user['id'], $nodes[0]);
        }

        $deviceForm = $this->getDeviceForm($device);
        $mappingForm = $this->getMappingForm($deviceId);
        $sensorOptionsForm = $this->getSensorOptionsForm($device);
        $csvFieldsForm = $this->getCsvFieldsForm($device);

        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $data = array(
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'extra_description' => $_POST['extra_description'],
                'location_provided' => $_POST['location_provided'],
                'lat' => empty($_POST['lat']) ? NULL : $_POST['lat'],
                'lng' => empty($_POST['lng']) ? NULL : $_POST['lng'],
                'radius' => empty($_POST['radius']) ? NULL : $_POST['radius'],
                'elevation' => empty($_POST['elevation']) ? NULL : $_POST['elevation'],
                'expose_location' => $_POST['expose_location'],
                'kanarek' => $_POST['kanarek'],
            );
            $this->deviceModel->updateDevice($deviceId, $data);
            $this->alert(__('Updated the device', 'success'));
            $device = $this->getDevice($deviceId);
            $deviceForm->setDefaultValues($device);
        }

        if ($device['location_provided']) {
            $deviceForm->getElement('radius')->addGroupClass('show');
            $deviceForm->getElement('elevation')->addGroupClass('show');
        }

        if ($mappingForm->isSubmitted() && $mappingForm->validate($_POST)) {
            $this->deviceModel->addMapping($deviceId, $_POST['db_name'], $_POST['json_name']);
            $this->alert(__('Created a new mapping', 'success'));    
        }

        if ($sensorOptionsForm->isSubmitted() && $sensorOptionsForm->validate($_POST)) {
            $data = array(
                'temperature_offset' => $_POST['temperature_offset'],
            );
            $this->deviceModel->updateDevice($deviceId, $data);
            $this->alert(__('Updated the device', 'success'));
            $device = $this->getDevice($deviceId);
            $sensorOptionsForm->setDefaultValues($device);
        }

        if ($csvFieldsForm->isSubmitted() && $csvFieldsForm->validate($_POST)) {
            $data = array('csv_fields' => DeviceController::checkboxValuesToList($_POST, array_keys(\AirQualityInfo\Model\Updater::VALUE_MAPPING)));
            $this->deviceModel->updateDevice($deviceId, $data);
            $this->alert(__('Updated CSV fields', 'success'));
            $device = $this->getDevice($deviceId);
            $csvFieldsForm->setDefaultValues(DeviceController::listToCheckboxValues($device['csv_fields']));
        }

        $mapping = $this->deviceModel->getMappingForDevice($deviceId);

        $this->render(array(
            'view' => 'admin/views/device/edit.php'
        ), array(
            'device' => $device,
            'deviceId' => $deviceId,
            'deviceForm' => $deviceForm,
            'mappingForm' => $mappingForm,
            'sensorOptionsForm' => $sensorOptionsForm,
            'csvFieldsForm' => $csvFieldsForm,
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
            'api_key' => bin2hex(random_bytes(16)),
        );
        $this->deviceModel->updateDevice($deviceId, $data);
        $this->alert(__('New API key has been set.', 'success'));
        header('Location: '.l('device', 'edit', null, array('device_id' => $deviceId)));
    }

    private function getDeviceForm($device) {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $deviceForm->addElement('esp8266_id', 'text', 'ESP 8266 id', array('disabled' => true));
        $this->addNameField($deviceForm);
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        $deviceForm->addElement('extra_description', 'text', 'Extra description');
        $deviceForm->addElement('kanarek', 'checkbox', 'Include in Kanarek');
        $deviceForm->addElement('location_provided', 'checkbox', 'Choose location', array('data-toggle'=>'collapse', 'data-target'=>'.map-control'), null);
        $deviceForm->addElement('radius', 'number', 'Radius (m)', array('min' => 50, 'max' => 500, 'step' => 50))
            ->addGroupClass('map-control')
            ->addGroupClass('collapse')
            ->addRule('required')
            ->addRule('range', array('min' => 50, 'max' => 500));
        $deviceForm->addElement('elevation', 'number', 'Elevation (m a.s.l.)', array('min' => -10994, 'max' => 8848))
            ->addGroupClass('map-control')
            ->addGroupClass('collapse')
            ->addRule('range', array('min' => -10994, 'max' => 8848));
        $deviceForm->addElement('expose_location', 'checkbox', 'Expose location in the data.json', array(), null)
            ->addGroupClass('map-control')
            ->addGroupClass('collapse');
        $deviceForm->addElement('lat', 'hidden');
        $deviceForm->addElement('lng', 'hidden');
        $deviceForm->setDefaultValues($device);
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

    private function getSensorOptionsForm($device) {
        $sensorOptionsForm = new \AirQualityInfo\Lib\Form\Form("sensorOptions");
        $sensorOptionsForm->addElement('temperature_offset', 'number', 'Temperature offset', array('min' => -10, 'max' => 10, 'step' => '.01'))
            ->addRule('required')
            ->addRule('range', array('min' => -10, 'max' => 10));
        $sensorOptionsForm->setDefaultValues($device);
        return $sensorOptionsForm;
    }

    private function getCsvFieldsForm($device) {
        $fields = array_keys(\AirQualityInfo\Model\Updater::VALUE_MAPPING);
        $csvFieldsForm = new \AirQualityInfo\Lib\Form\Form("csvFields");
        foreach ($fields as $field) {
            if (str_starts_with($field, 'gps')) {
                continue;
            }
            $csvFieldsForm->addElement($field, 'checkbox', $field, array(), null);
        }
        $csvFieldsForm->setDefaultValues(DeviceController::listToCheckboxValues($device['csv_fields']));
        return $csvFieldsForm;
    }

    private static function listToCheckboxValues($commaSeparatedList) {
        return array_fill_keys(explode(',', $commaSeparatedList), true);
    }

    private static function checkboxValuesToList($formValues, $allowedValues) {
        $keys = array();
        foreach ($formValues as $k => $v) {
            if ($v && in_array($k, $allowedValues)) {
                $keys[] = $k;
            }
        }
        return implode(',', $keys);
    }

    private function getDevice($deviceId) {
        $device = $this->deviceModel->getDeviceById($deviceId);
        if ($device == null || $device['user_id'] != $this->user['id']) {
            header('Location: '.l('device', 'index'));
            die();
        }
        return $device;
    }

    public function assign($assignToken) {
        $device = $this->deviceModel->getDeviceByAssignToken($assignToken);
        if ($device === false || $device['user_id'] != 9999) {
            header('Location: '.l('device', 'index'));
            die();
        }
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");

        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $data = array(
                'user_id' => $this->user['id'],
            );
            $this->deviceModel->updateDevice($device['id'], $data);
            $this->alert(__('Assigned the device', 'success'));
            header('Location: '.l('device', 'edit', null, array('device_id' => $device['id'])));
            return;
        }

        $this->render(array(
            'view' => 'admin/views/device/assign.php'
        ), array(
            'deviceForm' => $deviceForm,
            'assignToken' => $assignToken,
            'device' => $device,
        ));
    }

    private static function chunkedContent() {
        set_time_limit(60 * 60);
        header('Content-Type: text/event-stream; charset=utf-8');
        header('X-Accel-Buffering: no');
        flush();
        ob_end_flush();
    }
}

?>