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
            'head' => 'admin/partials/about/head.php',
            'tail' => 'admin/partials/about/tail.php'
        ));
    }

    public function support() {
        $this->render(array(
            'view' => 'admin/views/static/support-'.$this->currentLocale->getCurrentLang().'.php',
            'head' => 'admin/partials/about/head.php',
            'tail' => 'admin/partials/about/tail.php'
        ));
    }
}
?>