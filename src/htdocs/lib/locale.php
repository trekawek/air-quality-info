<?php
namespace AirQualityInfo\Lib {
    class Locale {

        const SUPPORTED_LANGUAGES = array('en' => 'English', 'hu' => 'Magyar', 'pl' => 'Polski', 'ro' => 'Română');

        const LANG_TO_LOCALE = array('en' => 'en_US', 'hu' => 'hu_HU', 'pl' => 'pl_PL', 'ro' => 'ro_RO');

        const LANG_TO_FLAG = array('en' => 'united-kingdom.png', 'hu' => 'hungary.png', 'pl' => 'poland.png', 'ro' => 'romania.png');

        private $currentLang;

        private $locale;

        private $jsLocale;

        function __construct() {
            $this->currentLang = Locale::resolveCurrentLang();
            $this->loadLocale();
        }

        function getCurrentLang() {
            return $this->currentLang;
        }

        function setLang($lang) {
            if (isset(Locale::SUPPORTED_LANGUAGES[$lang])) {
                setcookie("lang", $lang, time() + 60 * 60 * 24 * 365, '/');

                if ($this->currentLang != $lang) {
                    $this->currentLang = $lang;
                    $this->loadLocale();
                }
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

        function addLangPrefix($uri) {
            $path = urldecode(explode("?", $uri)[0]);
            $segments = explode('/', $path);

            // language prefix is already present
            if (count($segments) > 1) {
                if (isset(Locale::SUPPORTED_LANGUAGES[$segments[1]])) {
                    return $uri;
                }
            }

            $prefix = '/'.$this->getCurrentLang();
            if ($path === '/') {
                return $prefix.substr($uri, 1);
            } else {
                return $prefix.$uri;
            }
        }

        function updateLangPrefix($uri, $newLanguage) {
            $path = urldecode(explode("?", $uri)[0]);
            $segments = explode('/', $path);

            if (count($segments) > 1) {
                if (isset(Locale::SUPPORTED_LANGUAGES[$segments[1]])) {
                    $uri = substr($uri, 1 + strlen($segments[1]));
                    $path = urldecode(explode("?", $uri)[0]);
                }
            }

            $prefix = '/'.$newLanguage;
            if ($path === '/') {
                return $prefix.substr($uri, 1);
            } else {
                return $prefix.$uri;
            }
        }

        private function loadLocale() {
            setlocale(LC_ALL, Locale::LANG_TO_LOCALE[$this->currentLang]);
            ob_start();
            include_once(__DIR__."/../locale/".$this->currentLang.".php");
            ob_end_clean();
            $this->locale = $locale;
            $this->jsLocale = $jsLocale;
        }

        private static function resolveCurrentLang() {
            $currentLang = null;
            if (isset($_COOKIE['lang'])) {
                $currentLang = $_COOKIE['lang'];
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