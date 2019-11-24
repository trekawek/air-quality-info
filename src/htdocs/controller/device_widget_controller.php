<?php
namespace AirQualityInfo\Controller;

class DeviceWidgetController extends AbstractController {

    private $recordModel;

    private $userModel;

    public function __construct(
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\UserModel $userModel) {
        $this->recordModel = $recordModel;
        $this->userModel = $userModel;
    }

    public function show($device) {
        $nodeById = $this->deviceHierarchyModel->getAllNodesById($this->userId);
        $path = \AirQualityInfo\Model\DeviceHierarchyModel::calculateDevicePath($nodeById, $device['id']);

        $lastData = $this->recordModel->getLastData($device['id']);
        $averages = $this->recordModel->getAverages($device['id'], 1);
        $user = $this->userModel->getUserById($this->userId);
        $domain = $user['domain'];
        $this->render(array('view' => 'views/widget/device/index.php', 'layout' => false), array(
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