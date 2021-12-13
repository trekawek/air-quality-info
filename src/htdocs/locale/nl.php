<?php
/*$jsLocale = array();
$jsLocale['days'] = 'dagen';
$jsLocale['Are you sure to delete this resource?'] = 'Weet u zeker dat u deze bron wilt verwijderen?';
$jsLocale['Days'] = 'Dagen';
$jsLocale['Detector humidity'] = 'Detector vochtigheid';
$jsLocale['Detector temperature'] = 'Detector temperatuur';
$jsLocale['High'] = 'Hoog';
$jsLocale['Humidity'] = 'Vochtigheid';
$jsLocale['Low'] = 'Laag';
$jsLocale['Medium'] = 'Gemiddeld';
$jsLocale['Night view'] = 'Nachtweergave';
$jsLocale['PM₁₀ limit'] = 'PM₁₀ limiet';
$jsLocale['PM₂.₅ limit'] = 'PM₂.₅ limiet';
$jsLocale['Pollution level'] = 'Vervuilingsgraad';
$jsLocale['Pressure'] = 'Druk';
$jsLocale['Temperature'] = 'Temperatuur';
$jsLocale['Very high'] = 'Erg hoog';
$jsLocale['Very low'] = 'Erg laag';

$locale = array_merge($jsLocale);
$locale['<meta> tags to be put in the <head> section'] = '<meta> tags die in de <head> sectie worden geplaatst';
$locale['%s should be between %d and %d'] = '%s zou tussen %d en %d moeten zijn';
$locale['<a href="https://www.airqualitynow.eu/nl/about_indices_definition.php">CAQI</a> index'] = '';
$locale['About page body'] = 'Over paginatekst';
$locale['About page name (3)'] = 'Over paginanaam';
$locale['Account'] = 'Account';
$locale['Account info'] = 'Account informtie';
$locale['Add custom field mapping'] = 'Voeg aangepaste veldtoewijzing toe';
$locale['Add mapping'] = 'Voeg veldtoewijzing toe';
$locale['Add new device'] = 'Voeg nieuw apparaat toe';
$locale['Add new directory'] = 'Voeg nieuwe map toe';
$locale['Add new widget'] = 'Voeg nieuwe widget toe ';
$locale['Add sensor id'] = 'Voeg sensor id toe';
$locale['Add your own device'] = 'Voeg uw eigen apparaat toe';
$locale['Added new sensor id'] = 'Nieuw toegevoegde sensor id';
$locale['Air quality'] = 'Luchtkwaliteit';
$locale['Allows sensor.community sensors'] = 'Staat sensor.community sensoren toe';
$locale['An account with this e-mail address is already registered.'] = '';
$locale['Annual average'] = '';
$locale['Annual averages'] = '';
$locale['Annual stats'] = '';
$locale['Atmospheric pressure'] = '';
$locale['Average'] = '';
$locale['Back to the device'] = '';
$locale['Brand name (2)'] = '';
$locale['Choose location'] = '';
$locale['Change your password'] = '';
$locale['Click to see more details.'] = '';
$locale['Contact info'] = '';
$locale['Create'] = '';
$locale['Create directory'] = '';
$locale['Create your account'] = '';
$locale['Create widget'] = '';
$locale['Created a new device'] = '';
$locale['Created a new directory'] = '';
$locale['Created a new mapping'] = '';
$locale['Created a new widget'] = '';
$locale['CSV'] = '';
$locale['CSV archive'] = '';
$locale['Custom CSS style'] = '';
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
$locale['Extra description'] = '';
$locale['Fill to add a new item to the menu.'] = '';
$locale['Find sensor on'] = '';
$locale['Forgot password'] = '';
$locale['Forgot your password?'] = '';
$locale['Footer'] = '';
$locale['Footer (5)'] = '';
$locale['Graphs'] = '';
$locale['Guide'] = '';
$locale['Header (4)'] = '';
$locale['Hidden'] = '';
$locale['Home'] = '';
$locale['Horizontal'] = '';
$locale['Import data from Madavi.de'] = '';
$locale['Imports data from'] = '';
$locale['Import finished'] = '';
$locale['Index'] = '';
$locale['Instantaneous'] = '';
$locale['Invalid domain'] = '';
$locale['Invalid email or password'] = '';
$locale['Invalid path'] = '';
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
$locale['Name'] = '';
$locale['New password has been set.'] = '';
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
$locale['PM'] = '';
$locale['Pollution levels by days'] = '';
$locale['Port'] = '';
$locale['Powered by '] = '';
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
$locale['Sensor options'] = '';
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
$locale['Temperature offset'] = '';
$locale['Template widgets'] = '';
$locale['Templates'] = '';
$locale['The domain name has to consists of letters and digits.'] = '';
$locale['The minimum length of the password is 8 characters.'] = '';
$locale['The name should consist of alphanumeric characters and dashes'] = '';
$locale['The path should consist of alphanumeric characters, dashes and slashes'] = '';
$locale['Theme'] = '';
$locale['There are no data'] = '';
$locale['This domain is already used.'] = '';
$locale['This is default device'] = '';
$locale['This is the HTML content of the custom page linked above.'] = '';
$locale['This token is no longer valid.'] = '';
$locale['Time'] = '';
$locale['Timezone'] = '';
$locale['Title'] = '';
$locale['Update'] = '';
$locale['Update brand icon (1)'] = '';
$locale['Updated directory'] = '';
$locale['Update password'] = '';
$locale['Update password on aqi.eco'] = '';
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
$locale['Values'] = '';
$locale['Vertical'] = '';
$locale['Visibility'] = '';
$locale['Visible'] = '';
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
