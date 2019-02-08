<?php
class Locale {

    const SUPPORTED_LANGUAGES = array('en' => 'English', 'pl' => 'Polski');

    private $currentLang;

    private $locale;

    function __construct() {
        $this->currentLang = Locale::resolveCurrentLang();
        require_once("locale/".$this->currentLang.".php");
        $this->locale = $locale;
    }

    function getCurrentLang() {
        return $this->currentLang;
    }

    function setLang($lang) {
        if (isset(Locale::SUPPORTED_LANGUAGES[$lang])) {
            $_SESSION['lang'] = $lang;
            $this->currentLang = $lang;
        }
    }

    function getMessages() {
        return $this->locale;
    }

    function getMessage($msg) {
        if (isset($this->locale[$msg])) {
            return $this->locale[$msg];
        } else {
            if ($this->currentLang != 'en') {
                error_log("Unknown msg: [$msg] for locale [$currentLang]");
            }
            return $msg;
        }
    }

    private static function resolveCurrentLang() {
        $currentLang = null;
        if (isset($_SESSION['lang'])) {
            $currentLang = $_SESSION['lang'];
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

$currentLocale = new Locale();
if (isset($_GET['lang'])) {
  $currentLocale->setLang($_GET['lang']);
}

function __($msg) {
  global $currentLocale;
  return $currentLocale->getMessage($msg);
}
?>