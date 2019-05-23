<?php
namespace AirQualityInfo\Admin\Controller;

class AbstractController {

    private $templateVariables;

    protected $userModel;

    protected $authorizationRequired = true;

    protected $user;

    protected $title = null;

    public function render($args, $data = array()) {
        $args = array_merge(array(
            'layout' => true
        ), $args);

        $currentUser = $this->user;
        $title = $this->title;
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

    // @Inject
    public function setUserModel(\AirQualityInfo\Model\UserModel $userModel) {
        $this->userModel = $userModel;
    }

    public function beforeAction() {
        if ($this->authorizationRequired) {
            $this->authorize();
        }
    }

    protected function authorize() {
        if (isset($_SESSION['user_id'])) {
            $this->user = $this->userModel->getUserById($_SESSION['user_id']);
            if ($this->user != null) {
                return;
            }
        }
        header('Location: ' . l('user', 'login'));
        die();
    }

    protected function alert($message, $type = 'primary') {
        $_SESSION['alert'] = array('type' => $type, 'message' => $message);
    }

    private function getAlert() {
        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            unset($_SESSION['alert']);
            return $alert;
        } else {
            return null;
        }
    }
}
?>