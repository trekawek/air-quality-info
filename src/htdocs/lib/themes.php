<?php
class Theme {

  const THEMES = array('default' => 'Default theme', 'darkly' => 'Darkly theme');

  private $theme;

  function __construct() {
    $theme = null;
    if (isset($_SESSION['theme'])) {
      $theme = $_SESSION['theme'];
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
      $_SESSION['theme'] = $theme;
      $this->theme = $theme;
    }
  }
}

$currentTheme = new Theme();
if (isset($_GET['theme'])) {
  $currentTheme->setTheme($_GET['theme']);
}
?>