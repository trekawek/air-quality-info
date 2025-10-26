<?php
/*$jsLocale = array();
$jsLocale['days'] = '';
$jsLocale['Ambient light'] = '';
$jsLocale['Are you sure to delete this resource?'] = '';
$jsLocale['Days'] = '';
$jsLocale['Detector humidity'] = '';
$jsLocale['Detector temperature'] = '';
$jsLocale['Exit fullscreen'] = '';
$jsLocale['High'] = '';
$jsLocale['Humidity'] = '';
$jsLocale['Low'] = '';
$jsLocale['Medium'] = '';
$jsLocale['Night view'] = '';
$jsLocale['Noise level'] = '';
$jsLocale['PM₁₀ limit'] = '';
$jsLocale['PM₂.₅ limit'] = '';
$jsLocale['Pollution level'] = '';
$jsLocale['Pressure'] = '';
$jsLocale['Rainfall'] = '';
$jsLocale['Temperature'] = '';
$jsLocale['Very high'] = '';
$jsLocale['Very low'] = '';
$jsLocale['View fullscreen'] = '';
$jsLocale['Wind speed'] = '';
$jsLocale['DATE_TIME_FORMAT'] = 'dd, MMM D HH:mm';
$jsLocale['DATE_FORMAT'] = 'dd, MMM D';

$locale = array_merge($jsLocale);
$locale['<meta> tags to be put in the <head> section'] = '';
$locale['%s should be between %d and %d'] = '';
$locale['<a href="https://www.airqualitynow.eu/about_indices_definition.php">CAQI</a> index'] = '';
$locale['About page body'] = '';
$locale['About page name (3)'] = '';
$locale['Account'] = '';
$locale['Account info'] = '';
$locale['Add device adjustment'] = '';
$locale['Add adjustment'] = '';
$locale['Add custom field mapping'] = '';
$locale['Add mapping'] = '';
$locale['Add new device'] = '';
$locale['Add new directory'] = '';
$locale['Add new widget'] = '';
$locale['Add sensor id'] = '';
$locale['Add your own device'] = '';
$locale['Added new sensor id'] = '';
$locale['Air quality'] = '';
$locale['Allows sensor.community sensors'] = '';
$locale['An account with this e-mail address is already registered.'] = '';
$locale['Annual average'] = '';
$locale['Annual averages'] = '';
$locale['Annual stats'] = '';
$locale['Assign'] = '';
$locale['Assign device'] = '';
$locale['Assigned the device'] = '';
$locale['Atmospheric pressure'] = '';
$locale['Average'] = '';
$locale['Back to the device'] = '';
$locale['Brand name (2)'] = '';
$locale['Choose location'] = '';
$locale['Change your password'] = '';
$locale['Click to see more details.'] = '';
$locale['Contact info'] = '';
$locale['Contact'] = '';
$locale['Create'] = '';
$locale['Create directory'] = '';
$locale['Create your account'] = '';
$locale['Create widget'] = '';
$locale['Created a new adjustment'] = '';
$locale['Created a new device'] = '';
$locale['Created a new directory'] = '';
$locale['Created a new mapping'] = '';
$locale['Created a new widget'] = '';
$locale['CSV'] = '';
$locale['CSV archive'] = '';
$locale['CSV fields'] = '';
$locale['Custom CSS style'] = '';
$locale['Custom CSS style for widget'] = '';
$locale['Current air quality'] = '';
$locale['Current brand icon'] = '';
$locale['Custom field mappings'] = '';
$locale['Daily graph'] = '';
$locale['Dashboard'] = '';
$locale['Darkly theme'] = '';
$locale['Data'] = '';
$locale['Database field'] = '';
$locale['Day'] = '';
$locale['Days with PM₁₀ above 50µg/m³'] = '';
$locale['Debug info'] = '';
$locale['Deleted the device'] = '';
$locale['Deleted the mapping'] = '';
$locale['Deleted the node'] = '';
$locale['Deleted the sensor id'] = '';
$locale['Description'] = '';
$locale['Default theme'] = '';
$locale['Deleted widget'] = '';
$locale['Device adjustments'] = '';
$locale['Device hierarchy'] = '';
$locale['Device JSON list'] = '';
$locale['Device list'] = '';
$locale['Device name'] = '';
$locale['Device'] = '';
$locale['Device widget list'] = '';
$locale['Devices'] = '';
$locale['Domain'] = '';
$locale['Domain name'] = '';
$locale['Domain widget list'] = '';
$locale['E-mail'] = '';
$locale['Edit sensor'] = '';
$locale['Edit widget'] = '';
$locale['Enabling the sensor.community support disables the templating options.'] = '';
$locale['Enter e-mail to reset your password'] = '';
$locale['ESP 8266 id'] = '';
$locale['ESP8266 ID'] = '';
$locale['Edit device'] = '';
$locale['Edit directory'] = '';
$locale['E-mail has been sent. Please check your mailbox.'] = '';
$locale['Elevation (m a.s.l.)'] = '';
$locale['Expose location in the data.json'] = '';
$locale['Extra description'] = '';
$locale['Fill to add a new item to the menu.'] = '';
$locale['Find sensor on'] = '';
$locale['Forgot password'] = '';
$locale['Forgot your password?'] = '';
$locale['Footer'] = '';
$locale['Footer (5)'] = '';
$locale['Generate new token'] = '';
$locale['Generated TTN token'] = '';
$locale['Graphs'] = '';
$locale['Guide'] = '';
$locale['Header (4)'] = '';
$locale['Hidden'] = '';
$locale['Home'] = '';
$locale['Horizontal'] = '';
$locale['If you device has stopped sending data, please check <a href="https://aqi.eco/news">the news page</a>.'] = '';
$locale['Import data from Madavi.de'] = '';
$locale['Imports data from'] = '';
$locale['Import finished'] = '';
$locale['Include in Kanarek'] = '';
$locale['Index'] = '';
$locale['Instantaneous'] = '';
$locale['Invalid domain'] = '';
$locale['Invalid email or password'] = '';
$locale['Invalid path'] = '';
$locale['It is only required for the devices which are not configured to push data to sensor.community.'] = '';
$locale['JSON field'] = '';
$locale['JSONs'] = '';
$locale['Last'] = '';
$locale['Last data'] = '';
$locale['Last modification'] = '';
$locale['Last received data'] = '';
$locale['Last update'] = '';
$locale['Latitude'] = '';
$locale['Link'] = '';
$locale['Link device'] = '';
$locale['Link external device'] = '';
$locale['Link sensor.community device'] = '';
$locale['Linked device'] = '';
$locale['Loading...'] = '';
$locale['Locations'] = '';
$locale['login'] = '';
$locale['Login'] = '';
$locale['Logout'] = '';
$locale['Longitude'] = '';
$locale['Maintenance tools'] = '';
$locale['Make device default'] = '';
$locale['Map'] = '';
$locale['Maximum allowed size is 256 kB.'] = '';
$locale['Month'] = '';
$locale['Moving average'] = '';
$locale['Multiplier'] = '';
$locale['Name'] = '';
$locale['New password has been set.'] = '';
$locale['News'] = '';
$locale['Offset'] = '';
$locale['Only PNGs and SVGs are allowed'] = '';
$locale['Open'] = '';
$locale['Operations'] = '';
$locale['Page unavailable offline'] = '';
$locale['Password'] = '';
$locale['Password was successfully updated.'] = '';
$locale['Path'] = '';
$locale["Please choose value between 50 and 500."] = '';
$locale["Please click the link below to update your password on aqi.eco:\nhttps://%s%s"] = '';
$locale["Please create an account if you don't have one already."] = '';
$locale['Please connect to the internet to get the most recent info.'] = '';
$locale['Please paste the URL address of the device you want to link. It must be a link within aqi.eco domain, eg.: https://warsawa.aqi.eco/al-jerozolimskie'] = '';
$locale['Please provide a valid e-mail address.'] = '';
$locale['Please provide two identical passwords.'] = '';
$locale['Please use following data to configure your sensor'] = '';
$locale['Please use following data to configure your TTN application'] = '';
$locale['PM'] = '';
$locale['Pollution levels by days'] = '';
$locale['Port'] = '';
$locale['Powered by '] = '';
$locale['Predefined adjustment'] = '';
$locale['Predefined device adjustments'] = '';
$locale['Radius (m)'] = '';
$locale['Range'] = '';
$locale['Reading count'] = '';
$locale['Readings'] = '';
$locale['Redirect home page'] = '';
$locale['Received JSONs'] = '';
$locale['register'] = '';
$locale['Register'] = '';
$locale['Register now!'] = '';
$locale['Remove brand icon'] = '';
$locale['Repeat password'] = '';
$locale['Reset HTTP password'] = '';
$locale['Reset your password'] = '';
$locale['Root'] = '';
$locale['Send data to own API'] = '';
$locale['Sensor configuration'] = '';
$locale['Sensor ID'] = '';
$locale['Sensor id'] = '';
$locale['Sensord id list'] = '';
$locale['Server'] = '';
$locale['Settings'] = '';
$locale['Show all'] = '';
$locale['Show widget on the group page'] = '';
$locale['Show widget on the sensor page'] = '';
$locale['Sign in'] = '';
$locale['Sign in to your account'] = '';
$locale['Sign up'] = '';
$locale['Size'] = '';
$locale['Start'] = '';
$locale['Stats'] = '';
$locale['Support'] = '';
$locale['Template widgets'] = '';
$locale['Templates'] = '';
$locale['The domain name has to consists of letters and digits.'] = '';
$locale['The minimum length of the password is 8 characters.'] = '';
$locale['The name should consist of alphanumeric characters and dashes'] = '';
$locale['The path should consist of alphanumeric characters, dashes and slashes'] = '';
$locale['The TTN device ID should match the aqi.eco device name or <b>TTN Device ID</b> value set in the <a href="/device">Devices</a>.'] = '';
$locale['Theme'] = '';
$locale['There are no data'] = '';
$locale['This domain is already used.'] = '';
$locale['This is default device'] = '';
$locale['This is the HTML content of the custom page linked above.'] = '';
$locale['This token is no longer valid.'] = '';
$locale['Time'] = '';
$locale['Timezone'] = '';
$locale['Title'] = '';
$locale['TTN Device ID (optional)'] = '';
$locale['TTN token'] = '';
$locale['Update'] = '';
$locale['Update brand icon (1)'] = '';
$locale['Update CSV fields'] = '';
$locale['Updated CSV fields'] = '';
$locale['Updated directory'] = '';
$locale['Update password'] = '';
$locale['Update password on aqi.eco'] = '';
$locale['Update predefined adjustment'] = '';
$locale['Updated settings'] = '';
$locale['Updated template'] = '';
$locale['Updated the default device'] = '';
$locale['Updated the device'] = '';
$locale['Updated the password'] = '';
$locale['Updated widget'] = '';
$locale['Use PNG picture'] = '';
$locale['Use as a favicon'] = '';
$locale['User'] = '';
$locale['Value'] = '';
$locale['Value type'] = '';
$locale['Values'] = '';
$locale['Vertical'] = '';
$locale['Visibility'] = '';
$locale['Visible'] = '';
$locale['Webhook'] = '';
$locale['Weather'] = '';
$locale['Week'] = '';
$locale['Widget'] = '';
$locale['Widget footer (6)'] = '';
$locale['Widget template'] = '';
$locale['Widget source'] = '';
$locale['Widgets'] = '';
$locale['Widgets created here will show the average air quality calculated from all sensors.'] = '';
$locale['Widgets created here will show the state of a single device.'] = '';
$locale['Year'] = '';*/

$jsLocale = array();
$locale = array();

$locale['_widgets'] = [
    0 => [
        'label' => 'Okay',
        'icon' => 'fa-smile-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'spending time outdoors'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'spending time outdoors (pregnant, asthmatic, elderly)'
            ],
        ]
    ],
    1 => [
        'label' => 'Moderate',
        'icon' => 'fa-meh-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'spending time outdoors'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'spending time outdoors (pregnant, asthmatic, elderly)'
            ],
        ]
    ],
    2 => [
        'label' => 'Mediocre',
        'icon' => 'fa-meh-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Acceptable',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Acceptable',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'prolonged exposure in the open air'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'prolonged exposure in the open air (pregnant, asthmatic, elderly)'
            ],
        ]
    ],
    3 => [
        'label' => 'Unfavorable',
        'icon' => 'fa-frown-o',
        'recommendations' => [
            [
                'icon' => 'icon-home',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'Staying at home (elderly, pregnant women, people with heart and respiratory diseases)'
            ],
            [
                'icon' => 'icon-spacer',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-car',
                'color' => 'orange',
                'label' => 'Limit',
                'description' => 'driving a car'
            ],
        ]
    ],
    4 => [
        'label' => 'Bad',
        'icon' => 'fa-frown-o',
        'recommendations' => [
            [
                'icon' => 'icon-home',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'staying at home'
            ],
            [
                'icon' => 'icon-spacer',
                'color' => 'red',
                'label' => 'Postpone',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'red',
                'label' => 'Postpone',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-car',
                'color' => 'red',
                'label' => 'Postpone',
                'description' => 'driving a car'
            ],
        ]
    ]
];

?>