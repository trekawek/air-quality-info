<?php
namespace AirQualityInfo\Lib;

class CsrfToken {

    public static function generateTokenIfNotExists() {
        if (CsrfToken::getToken() === null) {
            CsrfToken::generateToken();
        }
    }

    public static function generateToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function getToken() {
        if (isset($_SESSION['csrf_token'])) {
            return $_SESSION['csrf_token'];
        } else {
            return null;
        }
    }

    public static function verifyToken() {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'))) {
            return;
        }
        // The token may arrive in the form body (regular form posts) or in the
        // X-CSRF-Token header (XHR requests such as the DELETE links, which
        // carry no form body).
        $token = null;
        if (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        } else if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        $expected = CsrfToken::getToken();
        if ($token === null || $expected === null || !hash_equals($expected, $token)) {
            http_response_code(403);
            die('Forbidden');
        }
    }
}

?>