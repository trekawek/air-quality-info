<?php
namespace AirQualityInfo\Admin\Controller;

class AbstractController {

    private $templateVariables;

    protected $userModel;

    protected $authorizationRequired = true;

    protected $user = null;

    protected $title = null;

    public function render($args, $data = array()) {
        $args = array_merge(array(
            'layout' => true,
            'head' => 'admin/partials/head.php',
            'tail' => 'admin/partials/tail.php',
        ), $args);

        $currentUser = $this->user;
        $title = $this->title;
        extract($this->templateVariables);
        extract($data);

        if ($args['layout']) {
            if (is_array($args['head'])) {
                foreach ($args['head'] as $f) {
                    include($f);
                }
            } else {
                include($args['head']);
            }
        }

        if (isset($args['body'])) {
            echo $args['body'];
        } else {
            include($args['view']);
        }

        if ($args['layout']) {
            if (is_array($args['tail'])) {
                foreach ($args['tail'] as $f) {
                    include($f);
                }
            } else {
                include($args['tail']);
            }
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

    public function setUser($authorizedUser) {
        $this->user = $authorizedUser;
    }

    public function beforeAction() {
        if ($this->authorizationRequired) {
            $this->authorize();
        }
    }

    protected function authorize() {
        if (!$this->user) {
            $_SESSION['redirect_uri'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . l('user', 'login'));
            die();
        }
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

    protected function getUriPrefix($domain = null) {
        if ($domain === null) {
            $domain = $this->user['domain'];
        }
        $uri_prefix = '//' . $domain . CONFIG['user_domain_suffixes'][0];
        $host = explode(':', $_SERVER['HTTP_HOST']);
        if (isset($host[1])) {
            $port = $host[1];
        }
        if (isset($port) && $port != 80 && $port != 443) {
            $uri_prefix .= ":$port";
        }
        return $uri_prefix;
    }
}
?>