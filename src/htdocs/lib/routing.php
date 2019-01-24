<?php
function parse_uri() {
    list($uri) = explode('?', $_SERVER['REQUEST_URI']);
    $uri = explode('/', $uri);
    $uri = array_values(array_filter($uri));

    $device = null;
    if (count($uri) > 0) {
        foreach (CONFIG['devices'] as $d) {
            if ($uri[0] == $d['name']) {
                $device = $d;
                array_shift($uri);
                break;
            }
        }
    }

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
    if (!($_SERVER['PHP_AUTH_USER'] == $device['user'] && $_SERVER['PHP_AUTH_PW'] == $device['password'])) {
        header('WWW-Authenticate: Basic realm="Air Quality Info Page"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
}

function get_route($routes, $current_action) {
    foreach ($routes as $uri => $route) {
        if ($uri == $current_action) {
            if (!isset($route['authenticate'])) {
                $route['authenticate'] = false;
            }
            return $route;
        }
    }

    return null;
}
?>