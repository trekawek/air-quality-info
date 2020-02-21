<?php
namespace AirQualityInfo\Admin\Controller;

class SensorController extends AbstractController {

    private $deviceModel;

    private $deviceHierarchyModel;

    private $sensorCommunityApi;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel,
            \AirQualityInfo\Lib\SensorCommunityApi $sensorCommunityApi) {
        $this->deviceModel = $deviceModel;
        $this->deviceHierarchyModel = $deviceHierarchyModel;
        $this->sensorCommunityApi = $sensorCommunityApi;
        $this->title = __('Devices');
    }

    public function map() {
        header('Content-type: application/json');
        readfile("https://maps.sensor.community/data/v2/data.dust.min.json");
    }

    public function create() {
        $deviceForm = new \AirQualityInfo\Lib\Form\Form("deviceForm");
        $deviceForm->addElement('sensor_id', 'text', 'Sensor id')->addRule('required');
        $this->addNameField($deviceForm)
            ->setOptions(array('prepend' => 'https://' . $this->user['domain'] . CONFIG['user_domain_suffixes'][0] . '/'));
        $deviceForm->addElement('description', 'text', 'Description')->addRule('required');
        if ($deviceForm->isSubmitted() && $deviceForm->validate($_POST)) {
            $deviceId = $this->deviceModel->createDevice(array(
                'user_id' => $this->user['id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'update_mode' => 'pull',
                'default_device' => 0,
                'location_provided' => 0
            ));
            foreach ($this->sensorCommunityApi->getMatchingSensors($_POST['sensor_id']) as $sensorId) {
                $this->deviceModel->insertSensor($deviceId, $sensorId);
            }
            $rootId = $this->deviceHierarchyModel->getRootId($this->user['id']);
            $this->deviceHierarchyModel->addChild($this->user['id'], $rootId, null, null, $deviceId);
            
            $this->alert(__('Linked device', 'success'));
            header('Location: '.l('device', 'edit', null, array('device_id' => $deviceId)));
        } else {
            $this->render(array(
                'view' => 'admin/views/sensor/create.php'
            ), array(
                'deviceForm' => $deviceForm
            ));
        }
    }

}

?>