<?php
function l($device, $action, $query_args = array()) {
  $link = '';
  if ($device != CONFIG['devices'][0]) {
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
?>