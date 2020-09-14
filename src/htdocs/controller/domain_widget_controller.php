<?php
namespace AirQualityInfo\Controller;

use \AirQualityInfo\Lib\PollutionLevel;

class DomainWidgetController extends AbstractController {

    private $recordModel;

    private $userModel;

    private $widgetModel;

    private $locale;

    public function __construct(
            \AirQualityInfo\Model\RecordModel $recordModel,
            \AirQualityInfo\Model\WidgetModel $widgetModel,
            \AirQualityInfo\Model\UserModel $userModel,
            \AirQualityInfo\Lib\Locale $currentLocale) {
        $this->recordModel = $recordModel;
        $this->userModel = $userModel;
        $this->widgetModel = $widgetModel;
        $this->locale = $currentLocale;
    }

    public function show($widgetId) {
        $widget = $this->widgetModel->getWidgetById($this->userId, $widgetId);
        if ($widget == null) {
            http_response_code(404);
            die();
        }

        $avgs = $this->getAverages();

        $maxLevel = -1;
        if ($avgs['pm10'] !== null) {
            $pm10Level = PollutionLevel::findLevel(PollutionLevel::PM10_THRESHOLDS_1H, $avgs['pm10']);
            $maxLevel = max($maxLevel, $pm10Level);
        }
        if ($avgs['pm25'] !== null) {
            $pm25Level = PollutionLevel::findLevel(PollutionLevel::PM25_THRESHOLDS_1H, $avgs['pm25']);
            $maxLevel = max($maxLevel, $pm25Level);
        }
        if ($maxLevel == -1) {
            $maxLevel = null;
        }
        $this->render(array('view' => 'views/widget/domain/'.$widget['template'].'.php', 'layout' => false), array(
            'title' => $widget['title'],
            'level' => $maxLevel,
            'locale' => $this->locale->getValue('_widgets')[$maxLevel],
            'siteUrl' => $this->getUriPrefix(),
            'widgetId' => $widgetId,
            'hideDetailsLink' => (isset($_GET['hide_details_link']) && $_GET['hide_details_link'] === 'true')
        ));
    }

    private function getAverages() {
        $sums = array();
        $counts = array();

        foreach ($this->deviceById as $deviceId => $device) {
            $avg1h = $this->recordModel->getAverages($deviceId, 1);
            foreach ($avg1h['values'] as $k => $v) {
                if (!isset($sums[$k])) {
                    $sums[$k] = null;
                    $counts[$k] = null;
                }
                if ($v !== null) {
                    if ($sums[$k] === null) {
                        $sums[$k] = 0;
                        $counts[$k] = 0;
                    }
                    $sums[$k] += $v;
                    $counts[$k]++;
                }
            }
        }

        $avgs = array();
        foreach ($sums as $k => $v) {
            if ($sums[$k] === null) {
                $avgs[$k] = null;
            } else {
                $avgs[$k] = $sums[$k] / $counts[$k];
            }
        }
        return $avgs;
    }

}
?>