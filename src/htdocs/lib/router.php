<?php
namespace AirQualityInfo\Lib {

    class Router {

        private $routes;

        private $devices;

        function __construct($routes, $devices) {
            $this->routes = $routes;
            $this->devices = $devices;
        }

        function findRoute($method, $uri) {
            $userDomain = explode('.', $host)[0];
            $uri = array_values(array_filter(explode('/', $uri)));
            foreach ($this->routes as $path => $route) {
                $path = explode(' ', $path);
                if ($path[0] !== $method) {
                    continue;
                }
                $arguments = $this->tryParse($firstHostSegment, $path[1], $uri);
                if ($arguments !== null) {
                    return array($route, $arguments);
                }
            }
            return array(null, null);
        }

        private function tryParse($userDomain, $path, $uri) {
            $path = array_values(array_filter(explode('/', $path)));
            $arguments = array();
            $i = 0;
            foreach ($path as $segment) {
                $optional = false;
                if (substr($segment, 0, 1) === '[' && substr($segment, -1, 1) === ']') {
                    $optional = true;
                    $segment = substr($segment, 1, -1);
                }
                if (substr($segment, 0, 1) === ':') {
                    $argName = substr($segment, 1);
                    if ($argName === 'device') {
                        list($device, $segmentCount) = $this->tryParseDevice(array_slice($uri, $i));
                        if ($device === null) {
                            return null;
                        }
                        $arguments['device'] = $device;
                        $i += $segmentCount;
                    } else {
                        $arguments[$argName] = $uri[$i++];
                    }
                } else if ($uri[$i++] === $segment) {
                    continue;
                } else {
                    return null;
                }
            }
            if ($i == count($uri)) {
                return $arguments;
            } else {
                return null;
            }
        }

        private function tryParseDevice($uri) {
            foreach ($this->devices as $device) {
                $deviceName = array_values(array_filter(explode('/', $device['name'])));
                if (Router::arrayStartsWith($deviceName, $uri)) {
                    return array($device, count($deviceName));
                }
            }
            return array(null, 0);
        }

        private static function arrayStartsWith($needle, $haystack) {
            $needle = array_values($needle);
            $haystack = array_values($haystack);
            if (count($needle) > count($haystack)) {
                return false;
            }
            foreach ($needle as $i => $e) {
                if ($haystack[$i] != $e) {
                    return false;
                }
            }
            return true;
        }

        function createLink($controller, $action, $device = null, $args = array(), $queryArgs = array()) {
            $isDefaultDevice = $this->devices[0]['id'] == $device['id'];

            $link = '';
            $path = null;
            foreach ($this->routes as $p => $r) {
                if ($r[0] == $controller && $r[1] == $action) {
                    $path = $p;
                    break;
                }
            }
            if ($path === null) {
                return $link;
            }
            $path = explode(' ', $path)[1];
            $path = array_values(array_filter(explode('/', $path)));
            foreach ($path as $segment) {
                $optional = false;
                if (substr($segment, 0, 1) === '[' && substr($segment, -1, 1) === ']') {
                    $optional = true;
                    $segment = substr($segment, 1, -1);
                }
                if (substr($segment, 0, 1) === ':') {
                    $argName = substr($segment, 1);
                    if ($argName === 'device') {
                        if (!$optional || !$isDefaultDevice) {
                            $link .= '/'.$device['name'];
                        }
                    } else {
                        $link .= '/'.$args[$argName];
                    }
                } else {
                    $link .= '/'.$segment;
                }
            }

            if ($link === '') {
                $link = '/';
            }

            $queryArgsAdded = false;
            foreach ($queryArgs as $k => $v) {
                if ($queryArgsAdded) {
                    $link .= '&';
                } else {
                    $link .= '?';
                    $queryArgsAdded = true;
                }
                $link .= "${k}=${v}";
            }
        
            return $link;    
        }

    }

}

namespace {
    function l($controller, $action, $device = null, $args = array(), $queryArgs = array()) {
        global $currentDevice;
        global $router;

        if ($device === null) {
            $device = $currentDevice;
        }

        return $router->createLink($controller, $action, $device, $args, $queryArgs);
    }
}
?>