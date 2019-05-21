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
        $this->render(array('view' => 'admin/views/user/register.php', 'layout' => false), array(
            'message' => __($reason),
            'email' => htmlspecialchars($_POST['email']),
            'domain' => htmlspecialchars($_POST['domain']),
            'password' => htmlspecialchars($_POST['password']),
            'password2' => htmlspecialchars($_POST['password2']),
        ));
    }
}

?>