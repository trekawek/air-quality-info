<?php
namespace AirQualityInfo\Admin\Controller;

class UserController extends AbstractController {

    public function __construct() {
        $this->authorizationRequired = false;
    }

    public function login() {
        $this->render(array('view' => 'admin/views/user/login.php', 'layout' => false));
    }

    public function logout() {
        unset($_SESSION['user_id']);
        header('Location: /');
    }

    public function doLogin() {
        $user = $this->userModel->getUserByEmail($_POST['email']);
        if ($user != null && password_verify($_POST['password'], $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            \AirQualityInfo\Lib\CsrfToken::generateToken();
            header('Location: /');
        } else {
            $this->render(array('view' => 'admin/views/user/login.php', 'layout' => false), array('message' => __('Invalid email or password')));
        }
    }

    public function register() {
        $this->render(array('view' => 'admin/views/user/register.php', 'layout' => false));
    }

    public function doRegister() {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->failRegistration("Please provide a valid e-mail address.");
            return;
        }

        $user = $this->userModel->getUserByEmail($email);
        if ($user !== null) {
            $this->failRegistration("An account with this e-mail address is already registered.");
            return;
        }
        if (strlen($_POST['password']) < 8) {
            $this->failRegistration("The minimum length of the password is 8 characters.");
            return;
        }
        if ($_POST['password'] != $_POST['password2']) {
            $this->failRegistration("Please provide two identical passwords.");
            return;
        }
        if (!preg_match('/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', $_POST['domain'])) {
            $this->failRegistration("The domain name has to consists of letters and digits.");
            return;
        }
        $userId = $this->userModel->createUser($email, $_POST['password'], $_POST['domain']);
        $_SESSION['user_id'] = $userId;
        header('Location: /');
    }

    private function failRegistration($reason) {
        $data = array();
        $data['message'] = __($reason);
        foreach (array('email', 'domain', 'password', 'password2') as $k) {
            $data[$k] = \AirQualityInfo\Lib\StringUtils::escapeHtmlAttribute($_POST[$k]);
        }
        $this->render(array('view' => 'admin/views/user/register.php', 'layout' => false), $data);
    }

    public function edit() {
        $this->authorize();

        $userForm = new \AirQualityInfo\Lib\Form\Form("userForm");
        $userForm->addElement('email', 'text', 'E-mail', array('disabled' => 1));
        $userForm->addElement('domain', 'text', 'Domain', array('disabled' => 1));
        $userForm->addElement('password', 'password', 'Password')
            ->addRule('required')
            ->addRule('minLength', 8)
            ->addRule('sameAs', 'password2');
        $userForm->addElement('password2', 'password', 'Repeat password')->addRule('required');
        $userForm->setDefaultValues($this->user);

        if ($userForm->isSubmitted() && $userForm->validate($_POST)) {
            $this->userModel->updatePassword($this->user['id'], $_POST['password']);
            $this->alert(__('Updated the password', 'success'));
            $userForm->setDefaultValues(array('password' => '', 'password2' => ''));
        }

        $this->render(array(
            'view' => 'admin/views/user/edit.php'
        ), array(
            'userForm' => $userForm
        ));
    }
}

?>