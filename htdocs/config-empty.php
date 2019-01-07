<?php
define('CONFIG', array(
  'devices' => array(
    array(
        'user'        => '...',
        'password'    => '...',
        'esp8266id'   => '...',
        'name'        => 'main',         # this will be used in URLs
        'description' => 'Main location', # user-friendly location name, will be used in navbar
        'value_mapping' => array(
            'pm10'        => 'SDS_P1',
            'pm25'        => 'SDS_P2',
            # 'temperature' => 'BME280_temperature',
            # 'humidity'    => 'BME280_humidity',
            # 'pressure'    => 'BME280_pressure',
            # 'heater_temperature' => 'temperature',
            # 'heater_humidity'    => 'humidity',
            # 'gps_time'    => 'GPS_time',
            # 'gps_date'    => 'GPS_date',
          ),
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
  'store_json_payload' => false,
# Google Analytics ID
  'ga_id' => ''
));
?>