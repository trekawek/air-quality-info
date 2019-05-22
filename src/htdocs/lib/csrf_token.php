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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['csrf_token'];
            if ($token === null || !hash_equals(CsrfToken::getToken(), $token)) {
                http_response_code(403);
                die('Forbidden');
            }
        }
    }
}

?>