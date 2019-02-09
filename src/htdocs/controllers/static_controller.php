<?php
class StaticController extends AbstractController {

    private $currentLocale;

    public function __construct($currentLocale) {
        parent::__construct();
        $this->currentLocale = $currentLocale;
    }

    public function about($device) {
        $this->render(array('view' => 'views/about_'.$this->currentLocale->getCurrentLang().'.php'));
    }

    public function offline() {
        $this->render(array('view' => 'views/offline.php'));
    }
}
?>