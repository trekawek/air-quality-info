<?php
namespace AirQualityInfo;

require('boot.php');

$host = explode(':', $_SERVER['HTTP_HOST'])[0];
if (in_array($host, CONFIG['admin_domains'])) {
    // handle the main domain
} else {
    require('routing/user_routing.php');
}
?>