<?php
function array_starts_with($needle, $haystack) {
    $needle = array_values($needle);
    $haystack = array_values($haystack);
    foreach ($needle as $i => $e) {
        if ($haystack[$i] != $e) {
            return false;
        }
    }
    return true;
}

function find_device($uri) {
    if (count($uri) == 0) {
        return array(null, $uri);
    }
    $found_device = null;
    $found_device_len = 0;
    foreach (CONFIG['devices'] as $d) {
        $name = array_map('trim', explode('/', $d['name']));
        $name_len = count($name);
        if (array_starts_with($name, $uri) && $found_device_len < $name_len) {
            $found_device = $d;
            $found_device_len = $name_len;
        }
    }
    return array($found_device, array_slice($uri, $found_device_len));
}

function parse_uri() {
    list($uri) = explode('?', $_SERVER['REQUEST_URI']);
    $uri = explode('/', $uri);
    $uri = array_values(array_filter($uri));

    $device = null;
    list($device, $uri) = find_device($uri);

    if ($device === null && count(CONFIG['devices']) == 1) {
        $device = CONFIG['devices'][0];
    }

    if (count($uri) > 0) {
        $current_action = implode('/', $uri);
    } else {
        $current_action = 'sensors';
    }

    if ($device == null) {
        header('Location: '
          .l(CONFIG['devices'][0], $current_action)
          .($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
        die();
    }
  
    return array($device, $current_action);
}

function l($device, $action, $query_args = array()) {
    $link = '';
    if (count(CONFIG['devices']) > 1) {
        $link .= '/'.$device['name'];
    }
  
    if ($action != 'sensors') {
        $link .= '/'.$action;
    }
    
    $query_arg_added = false;
    foreach ($query_args as $k => $v) {
        if ($query_arg_added) {
            $link .= '&';
        } else {
            $link .= '?';
            $query_arg_added = true;
        }
        $link .= "${k}=${v}";
    }
  
    if ($link == '') {
      $link = '/';
    }
  
    return $link;
}

function authenticate($device) {
    if (!(isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $device['user'] && $_SERVER['PHP_AUTH_PW'] == $device['password'])) {
        header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
}

function get_route($routes, $current_action) {
    $route = array_values($routes)[0];
    foreach ($routes as $uri => $r) {
        if ($uri == $current_action) {
            $route = $r;
            break;
        }
    }
    if (!isset($route['authenticate'])) {
        $route['authenticate'] = false;
    }
    return $route;
}
?>