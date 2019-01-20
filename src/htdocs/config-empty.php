<?php
define('CONFIG', array(
  'devices' => array(
    array(
        'user'        => 'YOUR_USERNAME',
        'password'    => 'YOUR_PASSWORD',
        'esp8266id'   => 'ESP8266_ID',
        'name'        => 'main',          # this will be used in URLs
        'description' => 'Main location', # user-friendly location name, will be used in navbar,
        #'contact_name' => 'John Doe',   # this name and e-mail will be displayed in the "about" page
        #'contact_email' => 'john.doe@example.com',
      ),
#    array(
#        'user'        => '...',
#        'password'    => '...',
#        'esp8266id'   => '...',
#        'name'        => 'second',
#        'description' => 'Second location',
#        'contact_name' => 'John Doe',
#        'contact_email' => 'john.doe@example.com',
#    ),
  ),
# Whether to store the last received JSON dump.
  'store_json_payload' => true,
# Google Analytics ID
  'ga_id' => '',
  'db' => array(
    'type' => 'rrd', # or 'mysql'
    # 'host' => 'localhost',
    # 'user' => 'air_quality_info',
    # 'password' => '...',
    # 'name' => 'air_quality_info'
  )
));
?>