<?php

require_once 'vendor/autoload.php';
require_once 'api-key.php';

use Dota2Api\Api;
use Dota2Api\Utils\Db;

Api::init(API_KEY, array('localhost', 'root', '', 'dota2_dev_db', ''), false);

$db_name = 'dota2_api_test_db';
$db = Db::obtain();
$db->exec('CREATE DATABASE '.$db_name);
Db::clean();
$db = Db::obtain('localhost', 'root', '', 'dota2_api_test_db', '');
$db->connectPDO();
$db->exec(file_get_contents('db_latest.sql'));

register_shutdown_function(function() {
    $db = Db::obtain();
    $db->exec('DROP DATABASE dota2_api_test_db');
});