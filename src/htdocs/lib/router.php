<?php
namespace AirQualityInfo\Lib {

    class Router {

        private $routes;

        private $devices;

        private $user;

        private $currentLocale;

        public function __construct($routes, $currentLocale, $devices = array(), $user = null) {
            $this->routes = $routes;
            $this->devices = $devices;
            $this->user = $user;
            $this->currentLocale = $currentLocale;
        }

        public function findRoute($method, $uri) {
            if ($uri == '/' && $method == 'GET' && $this->user && $this->user['redirect_root']) {
                $redirectedUri = $this->user['redirect_root'];
                $route = $this->doFindRoute('GET', $redirectedUri);
                if ($route[0] != null) {
                    return $route;
                }
                $redirectedUri = $currentLocale->addLangPrefix($redirectedUri);
                $route = $this->doFindRoute('GET', $redirectedUri);
                if ($route[0] != null) {
                    return $route;
                }
            }
            return $this->doFindRoute($method, $uri);
        }

        private function doFindRoute($method, $uri) {
            $uri = array_values(array_filter(explode('/', $uri)));
            foreach ($this->routes as $path => $route) {
                $path = explode(' ', $path);
                if ($path[0] !== $method) {
                    continue;
                }
                $arguments = $this->tryParse($path[1], $uri);
                if ($arguments !== null) {
                    return array($route, $arguments);
                }
            }
            return array(null, null);
        }

        private function tryParse($path, $uri) {
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
                    if ($argName === 'lang') {
                        if (isset($uri[$i]) && isset(Locale::SUPPORTED_LANGUAGES[$uri[$i]])) {
                            $arguments['lang'] = $uri[$i++];
                            continue;
                        } else if (!$optional) {
                            return null;
                        }
                    } else if ($argName === 'device') {
                        list($device, $segmentCount) = $this->tryParseDevice(array_slice($uri, $i));
                        if ($device === null) {
                            if ($optional) {
                                $device = $this->devices[0];
                                $segmentCount = 0;
                            } else {
                                return null;
                            }
                        }
                        $arguments['device'] = $device;
                        $i += $segmentCount;
                    } else if ($argName === 'path') {
                        $arguments['path'] = implode('/', array_slice($uri, $i));
                        return $arguments;
                    } else if (count($uri) > $i) {
                        $arguments[$argName] = $uri[$i++];
                    } else if (!$optional) {
                        return null;
                    }
                } else if (isset($uri[$i]) && $uri[$i] === $segment) {
                    $i++;
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
                if ($device['path'] === null) {
                    continue;
                }
                $devicePath = array_values(array_filter(explode('/', $device['path'])));
                if (Router::arrayStartsWith($devicePath, $uri)) {
                    return array($device, count($devicePath));
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
            $isDefaultDevice = count($this->devices) > 0 && $this->devices[0]['id'] == $device['id'];

            if (!isset($args['lang'])) {
                $args['lang'] = $this->currentLocale->getCurrentLang();
            }

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
                        if (!$optional || !$isDefaultDevice || ($this->user && $this->user['redirect_root'])) {
                            $link .= $device['path'];
                        }
                    } else if (isset($args[$argName])) {
                        $link .= '/'.$args[$argName];
                    } else if (!$optional) {
                        throw new \Exception("The argument $argName is not set.");
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

        public static function send404() {
            http_response_code(404);
            die();
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