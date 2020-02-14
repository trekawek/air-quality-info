<?php
namespace AirQualityInfo\Lib {
    class Locale {

        const SUPPORTED_LANGUAGES = array('en' => 'English', 'pl' => 'Polski');

        private $currentLang;

        private $locale;

        private $jsLocale;

        function __construct() {
            $this->currentLang = Locale::resolveCurrentLang();
            include_once(__DIR__."/../locale/".$this->currentLang.".php");
            $this->locale = $locale;
            $this->jsLocale = $jsLocale;
        }

        function getCurrentLang() {
            return $this->currentLang;
        }

        function setLang($lang) {
            if (isset(Locale::SUPPORTED_LANGUAGES[$lang])) {
                setcookie("lang", $lang, time() + 60 * 60 * 24 * 365);
                $this->currentLang = $lang;
            }
        }

        function getJsMessages() {
            return $this->jsLocale;
        }

        function getValue($key) {
            return $this->locale[$key];
        }

        function getMessage($msg) {
            if (isset($this->locale[$msg])) {
                return $this->locale[$msg];
            } else {
                if ($this->currentLang != 'en') {
                    error_log("Unknown msg: [$msg] for locale [".$this->currentLang."]");
                }
                return $msg;
            }
        }

        private static function resolveCurrentLang() {
            $currentLang = null;
            if (isset($_COOKIE['lang'])) {
                $currentLang = $_COOKIE['lang'];
            }
            if (isset($_GET['lang'])) {
                $currentLang = $_GET['lang'];
            }
            if (!isset(Locale::SUPPORTED_LANGUAGES[$currentLang])) {
                $currentLang = null;
            }
            if ($currentLang === null && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $currentLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            }
            if ($currentLang === null || !isset(Locale::SUPPORTED_LANGUAGES[$currentLang])) {
                $currentLang = 'en';
            }
            return $currentLang;  
        }
    }
}

namespace {
    function __($msg) {
        global $currentLocale;
        return $currentLocale->getMessage($msg);
    }
}
?>