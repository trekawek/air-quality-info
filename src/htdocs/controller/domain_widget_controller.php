<?php
namespace AirQualityInfo\Controller;

use \AirQualityInfo\Lib\PollutionLevel;

class DomainWidgetController extends AbstractController {

    private $recordModel;

    private $userModel;

    private $widgetModel;

    public function __construct(
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\WidgetModel $widgetModel,
            \AirQualityInfo\Model\UserModel $userModel) {
        $this->recordModel = $recordModel;
        $this->userModel = $userModel;
        $this->widgetModel = $widgetModel;
    }

    public function show($widgetId) {
        $widget = $this->widgetModel->getWidgetById($this->userId, $widgetId);
        if ($widget == null) {
            http_response_code(404);
            die();
        }
        
        $user = $this->userModel->getUserById($this->userId);

        $pm10 = $pm25 = null;
        $pm10count = $pm25count = 0;
        foreach ($this->deviceById as $deviceId => $device) {
            $avg1h = $this->recordModel->getAverages($deviceId, 1);
            if ($avg1h['values']['pm10'] !== null) {
                $pm10 += $avg1h['values']['pm10'];
                $pm10count++;
            }
            if ($avg1h['values']['pm25'] !== null) {
                $pm25 += $avg1h['values']['pm25'];
                $pm25count++;
            }
        }
        $maxLevel = -1;
        if ($pm10count > 0) {
            $pm10 /= $pm10count;
            $pm10Level = PollutionLevel::findLevel(PollutionLevel::PM10_THRESHOLDS_1H, $pm10);
            $maxLevel = max($maxLevel, $pm10Level);
        }
        if ($pm25count > 0) {
            $pm25 /= $pm25count;
            $pm25Level = PollutionLevel::findLevel(PollutionLevel::PM25_THRESHOLDS_1H, $pm25);
            $maxLevel = max($maxLevel, $pm25Level);
        }
        if ($maxLevel == -1) {
            $maxLevel = null;
        }
        $this->render(array('view' => 'views/widget/domain/'.$widget['template'].'/index.php', 'layout' => false), array(
            'title' => $widget['title'],
            'level' => $maxLevel,
            'siteUrl' => $this->getUriPrefix(),
            'widgetId' => $widgetId
        ));
    }

}
?>