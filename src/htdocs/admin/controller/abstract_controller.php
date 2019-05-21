<?php
namespace AirQualityInfo\Admin\Controller;

class AbstractController {

    private $templateVariables;

    public function render($args, $data = array()) {
        $args = array_merge(array(
            'layout' => true
        ), $args);

        extract($this->templateVariables);
        extract($data);

        if ($args['layout']) {
            include('admin/partials/head.php');
        }

        include($args['view']);

        if ($args['layout']) {
            include('admin/partials/tail.php');
        }
    }

    // @Inject
    public function setTemplateVariables($templateVariables) {
        $this->templateVariables = $templateVariables;
    }

}
?>