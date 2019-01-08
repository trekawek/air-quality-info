<?php
function __($msg) {
    global $locale;
    global $current_lang;

    if (isset($locale[$msg])) {
        return $locale[$msg];
    } else {
        if ($current_lang != 'en') {
            error_log("Unknown msg: [$msg] for locale [$current_lang]");
        }
        return $msg;
    }
}

$supported_languages = array('en' => 'English', 'pl' => 'Polski');

$current_lang = null;
if (isset($_SESSION['lang'])) {
    $current_lang = $_SESSION['lang'];
}
if (isset($_GET['lang'])) {
    $current_lang = $_GET['lang'];
}
if (!isset($supported_languages[$current_lang])) {
    $current_lang = null;
}
if ($current_lang === null) {
    $current_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (!isset($supported_languages[$current_lang])) {
        $current_lang = 'en';
    }
}
$_SESSION['lang'] = $current_lang;

require_once("locale/${current_lang}.php");
?>