<?php
define('THEMES', array('default' => _('Default theme'), 'darkly' => _('Darkly theme')));

$current_theme = $_SESSION['theme'];
if (isset($_GET['theme'])) {
  $current_theme = $_GET['theme'];
}
if (!isset(THEMES[$current_theme])) {
  $current_theme = 'default';
}
$_SESSION['theme'] = $current_theme;
?>