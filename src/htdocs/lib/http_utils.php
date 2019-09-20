<?php
namespace AirQualityInfo\Lib;

class HttpUtils {

    public static function setACAOHeader() {
        $adminOrigin = sprintf("%s://%s", $_SERVER['REQUEST_SCHEME'], CONFIG['admin_domains'][0]);
        $host = explode(':', $_SERVER['HTTP_HOST']);
        if (isset($host[1])) {
            $port = $host[1];
        }
        if (isset($port) && $port != 80 && $port != 443) {
            $adminOrigin .= ":$port";
        }
        header('Access-Control-Allow-Origin: '.$adminOrigin);
    }
    
}

?>