<?php
namespace AirQualityInfo\Admin\Controller;

class WidgetController extends AbstractController {

    private $deviceModel;

    private $deviceHierarchyModel;

    private $widgetModel;

    public function __construct(
            \AirQualityInfo\Model\DeviceModel $deviceModel,
            \AirQualityInfo\Model\DeviceHierarchyModel $deviceHierarchyModel,
            \AirQualityInfo\Model\WidgetModel $widgetModel) {
        $this->deviceModel = $deviceModel;
        $this->deviceHierarchyModel = $deviceHierarchyModel;
        $this->widgetModel = $widgetModel;
    }

    public function index() {
        $widgets = $this->widgetModel->getWidgetsForUser($this->user['id']);
        $devices = $this->deviceModel->getDevicesForUser($this->user['id']);
        $this->render(array('view' => 'admin/views/widget/index.php'), array(
            'widgets' => $widgets,
            'devices' => $devices
        ));
    }

    public function create() {
        $widgetForm = $this->getWidgetForm();
        if ($widgetForm->isSubmitted() && $widgetForm->validate($_POST)) {
            $widgetId = $this->widgetModel->createWidget(
                $this->user['id'],
                $_POST['title'],
                $_POST['footer'],
            );
            $this->alert(__('Created a new widget', 'success'));
            header('Location: '.l('widget', 'edit', null, array('widget_id' => $widgetId)));
        } else {
            $this->render(array(
                'view' => 'admin/views/widget/domain/create.php'
            ), array(
                'widgetForm' => $widgetForm
            ));
        }
    }

    private function getWidgetForm() {
        $widgetForm = new \AirQualityInfo\Lib\Form\Form("widgetForm");
        $widgetForm->addElement('title', 'text', 'Title')->addRule('required');
        $widgetForm->addElement('footer', 'textarea', 'Footer');
        return $widgetForm;
    }

    public function edit($widgetId) {
        $widgetForm = $this->getWidgetForm();
        $widget = $this->widgetModel->getWidgetById($this->user['id'], $widgetId);
        $widgetForm->setDefaultValues($widget);

        if ($widgetForm->isSubmitted() && $widgetForm->validate($_POST)) {
            $this->widgetModel->updateWidget(
                $this->user['id'],
                $widgetId,
                $_POST['title'],
                $_POST['footer'],
            );
            $this->alert(__('Updated widget', 'success'));
        }

        $widgetUri = 'https:'.$this->getUriPrefix()."/widget/".$widgetId;

        $this->render(array(
            'view' => 'admin/views/widget/domain/edit.php'
        ), array(
            'widgetForm' => $widgetForm,
            'widgetId' => $widgetId,
            'widgetUri' => $widgetUri,
        ));
    }

    public function delete($widgetId) {
        $this->widgetModel->deleteWidget($this->user['id'], $widgetId);
        $this->alert(__('Deleted widget'));
    }

    public function showDeviceWidget($deviceId) {
        $paths = $this->deviceHierarchyModel->getDevicePaths($this->user['id'], $deviceId);
        $path = null;
        if (!empty($paths)) {
            $path = $paths[0];
        }

        $widgetUri = 'https:'.$this->getUriPrefix().$path.'/widget';

        $this->render(array(
            'view' => 'admin/views/widget/device/show.php'
        ), array(
            'widgetUri' => $widgetUri
        ));
    }

}
?>