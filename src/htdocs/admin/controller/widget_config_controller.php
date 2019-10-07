<?php
namespace AirQualityInfo\Admin\Controller;

class WidgetConfigController extends AbstractController {

    private $deviceHierarchyModel;

    public function __construct(
            \AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel) {
        $this->deviceHierarchyModel = $deviceHierarchyModel;
    }

    public function show($deviceId) {
        $paths = $this->deviceHierarchyModel->getDevicePaths($this->user['id'], $deviceId);
        $path = null;
        if (!empty($paths)) {
            $path = $paths[0];
        }

        $widgetUri = 'https:'.$this->getUriPrefix().$path.'/widget';

        $this->render(array(
            'view' => 'admin/views/widget_config/show.php'
        ), array(
            'widgetUri' => $widgetUri
        ));
    }

}
?>