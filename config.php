<?php
define('CONFIG', array(
  'db' => array(
    'type' => 'mysql',
    'host' => 'db',
    'user' => 'air_quality_info',
    'password' => 'test',
    'name' => 'air_quality_info'
  ),
  'enable_pwa' => false,
  'admin_domains' => array('aqi.eco.local'),
  'user_domain_suffixes' => array('.aqi.eco.local'),
  'paypal_donate_id' => null,
  'ga_id' => null,
));
?>
