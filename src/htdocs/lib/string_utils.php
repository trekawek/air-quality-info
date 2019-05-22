<?php
namespace AirQualityInfo\Lib;

class StringUtils {

    public static function camelize($string) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    public static function decamelize($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }

    public static function escapeHtmlAttribute($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

?>