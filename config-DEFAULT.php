<?php
define('CONFIG', array(
  'db' => array(
    'type' => 'mysql',
    'host' => 'db',
    'user' => 'air_quality_info',
    'password' => 'test',
    'name' => 'air_quality_info'
  ),
  's3' => array(
    'version' => 'latest',
    'region'  => 'us-east-1',
    'endpoint' => 'http://minio:9000',
    'use_path_style_endpoint' => true,
    'credentials' => array(
      'key'    => 'minio',
      'secret' => 'minio123',
    )
  ),
  's3Bucket' => 'test-bucket',
  'enable_pwa' => false,
  'admin_domains' => array('aqi.eco.localhost'),
  'user_domain_suffixes' => array('.aqi.eco.localhost'),
  'paypal_donate_id' => null,
  'ga_id' => null,
  'google_maps_key' => null
));
?>
