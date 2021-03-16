<?php
namespace AirQualityInfo\Admin\Controller;

class UserController extends AbstractController {

    private $currentLocale;

    private $userTokenModel;

    private $mailgun;

    public function __construct(\AirQualityInfo\Lib\Locale $currentLocale, \AirQualityInfo\Model\UserTokenModel $userTokenModel, $mailgun) {
        $this->authorizationRequired = false;
        $this->currentLocale = $currentLocale;
        $this->userTokenModel = $userTokenModel;
        $this->mailgun = $mailgun;
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

            if (isset($_SESSION['redirect_uri'])) {
                header('Location: '.$_SESSION['redirect_uri']);
                unset($_SESSION['redirect_uri']);
            } else {
                header('Location: '.l('device', 'index'));
            }
        } else {
            $this->alert(__('Invalid email or password'), 'danger');
            $this->render(array('view' => 'admin/views/user/login.php', 'layout' => false));
        }
    }

    public function forgotPassword() {
        $this->render(array('view' => 'admin/views/user/forgot-password.php', 'layout' => false));
    }

    public function doForgotPassword() {
        if (isset($_POST['email'])) {
            $user = $this->userModel->getUserByEmail($_POST['email']);
            if ($user) {
                $token = $this->userTokenModel->generateToken($user['id']);
                $this->mailgun->messages()->send('web.aqi.eco', [
                    'from'    => 'no-reply@web.aqi.eco',
                    'to'      => $user['email'],
                    'subject' => __('Update password on aqi.eco'),
                    'text'    => sprintf(__("Please click the link below to update your password on aqi.eco:\nhttps://%s%s"), CONFIG['admin_domains'][0], l("user", "resetPassword", null, array('token' => $token)))
                ]);
            }
            $this->alert(__('E-mail has been sent. Please check your mailbox.'), 'success');
            header('Location: '.l('user', 'login'));
        } else {
            header('Location: '.l('user', 'forgotPassword'));
        }
    }

    public function resetPassword($token) {
        $userId = $this->userTokenModel->getUserIdByToken($token);
        if ($userId !== null) {
            $this->render(array('view' => 'admin/views/user/reset-password.php', 'layout' => false), array('token' => $token));
        } else {
            $this->alert(__('This token is no longer valid.'), 'danger');
            header('Location: '.l('user', 'login'));
        }
    }

    public function doResetPassword($token) {
        $userId = $this->userTokenModel->getUserIdByToken($token);
        if ($userId !== null) {
            if (strlen($_POST['password']) < 8) {
                $this->alert(__('The minimum length of the password is 8 characters.'), 'danger');
                $this->render(array('view' => 'admin/views/user/reset-password.php', 'layout' => false), array('token' => $token));
                return;
            }
            if ($_POST['password'] != $_POST['password2']) {
                $this->alert(__('Please provide two identical passwords.'), 'danger');
                $this->render(array('view' => 'admin/views/user/reset-password.php', 'layout' => false), array('token' => $token));
                return;
            }
            $this->userModel->updatePassword($userId, $_POST['password']);
            $this->userTokenModel->deleteToken($token);
            $this->alert(__('Password was successfully updated.'), 'success');
            header('Location: '.l('user', 'login'));
        } else {
            $this->alert(__('This token is no longer valid.'), 'danger');
            header('Location: '.l('user', 'login'));
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
        if ($this->userModel->getIdByDomain($_POST['domain']) !== null) {
            $this->failRegistration("This domain is already used.");
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

    public function settings() {
        $this->authorize();

        $timezones = \DateTimeZone::listIdentifiers();
        $timezones = array_combine($timezones, $timezones);

        $userForm = new \AirQualityInfo\Lib\Form\Form("userForm");
        $userForm->addElement('allow_sensor_community', 'checkbox', 'Allow pull sensors', array(), 'Enabling the support for pulling measurements from 3rd party sensors (eg. sensor.community) disables the templating options.');
        $urlPrefix = 'https://'
            .$this->user['domain']
            .CONFIG['user_domain_suffixes'][0]
            .'/'
            .$this->currentLocale->getCurrentLang();
        $userForm->addElement('redirect_root', 'text', 'Redirect home page')
            ->addRule('regexp', array('pattern' => '/^\/[a-z0-9\/-]+$/', 'message' => __('The path should consist of alphanumeric characters, dashes and slashes')))
            ->setOptions(array('prepend' => $urlPrefix));
        $userForm->addElement('timezone', 'select', 'Timezone')
            ->addRule('required')
            ->setOptions($timezones);

        $userForm->setDefaultValues($this->user);

        if ($userForm->isSubmitted() && $userForm->validate($_POST)) {
            $data = array(
                'redirect_root' => $_POST['redirect_root'],
                'timezone' => $_POST['timezone'],
                'allow_sensor_community' => $_POST['allow_sensor_community'],
            );
            $this->userModel->updateUser($this->user['id'], $data);
            $this->alert(__('Updated settings', 'success'));
        }

        $this->render(array(
            'view' => 'admin/views/user/settings.php'
        ), array(
            'userForm' => $userForm
        ));
    }
}

?>