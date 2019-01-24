<?php
function create_dao($device) {
    switch (CONFIG['db']['type']) {
        case 'mysql':
        require_once('db/mysql.php');
        $mysqli = new mysqli(CONFIG['db']['host'], CONFIG['db']['user'], CONFIG['db']['password'], CONFIG['db']['name']);
        $dao = new MysqlDao($device['esp8266id'], $mysqli);
        break;
    
        default:
        case 'rrd':
        require_once('db/rrd.php');
        $dao = new RRRDao($device['esp8266id']);
        break;
    }
    return $dao;
}
?>