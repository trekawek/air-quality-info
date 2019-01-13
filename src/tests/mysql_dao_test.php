<?php

require_once($_ENV['HTDOCS'].'/db/dao.php');
require_once($_ENV['HTDOCS'].'/db/mysql.php');

assert_options(ASSERT_BAIL, 1);

$mysqli = new mysqli($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE']);
$dao = new MysqlDao("123", $mysqli);

function cleanup() {
    global $mysqli;
    $mysqli->query("DELETE FROM aggregates");
    $mysqli->query("DELETE FROM records");
    $mysqli->query("DELETE FROM record_values");
}

foreach (array('test_vecMultiplyAndSum') as $test) {
    echo "Running test $test\n";
    cleanup();
    $test();
    echo "Passed\n";
}

function test_vecMultiplyAndSum() {
    $acc = array('v1' => 1,  'v2' => 2);
    $arr = array('v1' => 10, 'v2' => 20, 'v3' => 30);
    $result = MysqlDao::vecMultiplyAndSum($acc, $arr, 3);
    assert (count($arr) == 3);
    assert ($result === array('v1' => 31, 'v2' => 62, 'v3' => 90));
}

?>