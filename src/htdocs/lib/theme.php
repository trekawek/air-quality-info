<?php
namespace AirQualityInfo\Lib;

class Theme {

  const THEMES = array('default' => 'Default theme', 'darkly' => 'Darkly theme');

  private $theme;

  function __construct() {
    $theme = null;
    if (isset($_COOKIE['theme'])) {
      $theme = $_COOKIE['theme'];
    }
    if (!isset(Theme::THEMES[$theme])) {
      $theme = 'default';
    }
    $this->theme = $theme;
  }

  function getTheme() {
    return $this->theme;
  }

  function setTheme($theme) {
    if (isset(Theme::THEMES[$theme])) {
      setcookie("theme", $theme, time() + 60 * 60 * 24 * 365, '/');
      $this->theme = $theme;
    }
  }
}
?>