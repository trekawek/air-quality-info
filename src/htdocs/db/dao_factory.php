<?php
function create_dao() {
    static $mysqli = null;

    switch (CONFIG['db']['type']) {
        case 'mysql':
        require_once('db/mysql.php');
        if ($mysqli === null) {
            $mysqli = new mysqli(CONFIG['db']['host'], CONFIG['db']['user'], CONFIG['db']['password'], CONFIG['db']['name']);
        }
        $dao = new MysqlDao($mysqli);
        break;
    
        default:
        case 'rrd':
        require_once('db/rrd.php');
        $dao = new RRRDao();
        break;
    }
    return $dao;
}
?>