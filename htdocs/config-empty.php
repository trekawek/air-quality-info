<?php
define('CONFIG', array(
  'devices' => array(
    array(
        'user'        => '...',
        'password'    => '...',
        'esp8266id'   => '...',
        'name'        => 'main',         # this will be used in URLs
        'description' => 'Main location' # user-friendly location name, will be used in navbar
    ),
#    array(
#        'user'        => '...',
#        'password'    => '...',
#        'esp8266id'   => '...',
#        'name'        => 'second',
#        'description' => 'Second location'
#    ),
  ),
# Whether to store the last received JSON dump.
  'store_json_payload' => false,
# Google Analytics ID
  'ga_id' => ''
));
?>