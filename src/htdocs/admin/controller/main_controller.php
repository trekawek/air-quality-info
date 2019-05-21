<?php
namespace AirQualityInfo\Admin\Controller;

class MainController extends AbstractController {

    public function index() {
        $this->render(array('view' => 'admin/views/index.php'));
    }
}

?>