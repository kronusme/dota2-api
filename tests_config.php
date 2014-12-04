<?php

require_once 'vendor/autoload.php';
require_once 'api-key.php';
api::init(API_KEY, array('localhost', 'root', '', 'dota2_dev_db', ''), false);

$db_name = 'dota2_api_test_db';
$db = db::obtain();
$db->exec('CREATE DATABASE '.$db_name);
db::clean();
$db = db::obtain('localhost', 'root', '', 'dota2_api_test_db', '');
$db->connect_pdo();
$db->exec(file_get_contents('db_latest.sql'));

register_shutdown_function(function() {
    $db = db::obtain();
    $db->exec('DROP DATABASE dota2_api_test_db');
});