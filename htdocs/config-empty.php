<?php
define('CONFIG', array(
  'devices' => array(
    array(
        'user'        => 'YOUR_USERNAME',
        'password'    => 'YOUR_PASSWORD',
        'esp8266id'   => 'ESP8266_ID',
        'name'        => 'main',         # this will be used in URLs
        'description' => 'Main location', # user-friendly location name, will be used in navbar
        # 'maintenance' => 'Trwa przerwa techniczna. Detektor zostanie uruchomiony ponownie w ciągu godziny.'
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
  'store_json_payload' => true,
# Google Analytics ID
  'ga_id' => ''
));
?>