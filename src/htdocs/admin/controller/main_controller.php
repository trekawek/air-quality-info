<?php
namespace AirQualityInfo\Admin\Controller;

class MainController extends AbstractController {

    private $currentLocale;

    public function __construct($currentLocale) {
        $this->authorizationRequired = false;
        $this->currentLocale = $currentLocale;
    }

    public function index() {
        if (isset($_SESSION['user_id'])) {
            header('Location: '.l('device', 'index'));
            exit;
        }
        $this->render(array(
            'view' => 'admin/views/index-'.$this->currentLocale->getCurrentLang().'.php',
            'layout' => false
        ));
    }
}
?>