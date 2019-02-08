<?php
class AbstractController {

    const GLOBAL_VARIABLES = array('currentLocale', 'currentTheme', 'currentController', 'currentAction', 'currentDevice');

    protected $deviceTree;

    public function __construct() {
        $this->deviceTree = (new Navigation)->createDeviceTree();
    }

    public function render($args, $data = array()) {
        $args = array_merge(array(
            'layout' => true
        ), $args);

        foreach (AbstractController::GLOBAL_VARIABLES as $var) {
            $data[$var] = $GLOBALS[$var];
        }

        $deviceTree = $this->deviceTree;
        extract($data);

        if ($args['layout']) {
            include('partials/head.php');
        }

        include($args['view']);

        if ($args['layout']) {
            include('partials/tail.php');
        }
    }

    public function authenticate($device) {
        if (!(isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $device['user'] && $_SERVER['PHP_AUTH_PW'] == $device['password'])) {
            header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }
    }
}
?>