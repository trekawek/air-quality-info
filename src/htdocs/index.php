<?php
namespace AirQualityInfo;

require('boot.php');

$host = explode(':', $_SERVER['HTTP_HOST'])[0];
if (in_array($host, CONFIG['admin_domains'])) {
    require('routing/admin_routing.php');
} else {
    require('routing/user_routing.php');
}
?>