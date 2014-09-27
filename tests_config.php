<?php
define('testing', true);
require_once ('config.php');

$db_name = 'dota2_api_test_db';
$db_host = config::get('db_host');
$db_user = config::get('db_user');
$db_pass = config::get('db_pass');
$db_table_prefix = config::get('db_table_prefix');
$db = db::obtain();
$db->exec('CREATE DATABASE '.$db_name);
db::clean();
$db = db::obtain($db_host, $db_user, $db_pass, $db_name, $db_table_prefix);
$db->connect_pdo();
$db->exec(file_get_contents('db_latest.sql'));

register_shutdown_function(function() {
    $db = db::obtain();
    $db->exec('DROP DATABASE dota2_api_test_db');
});