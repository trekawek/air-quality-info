<?php
namespace AirQualityInfo\Controller;

class StaticController extends AbstractController {

    private $currentLocale;

    public function __construct($currentLocale) {
        $this->currentLocale = $currentLocale;
    }

    public function offline() {
        $this->render(array('view' => 'views/offline.php'));
    }
}
?>