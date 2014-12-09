<?php

namespace Dota2Api;

use Dota2Api\Utils\Db;
use Dota2Api\Utils\Request;

class Api {

    public static function init($api_key, array $db, $autoselect_db = true) {
        $db = call_user_func_array('Db::obtain', $db);
        $db->connect_pdo($autoselect_db);
        Request::$api_key = strval($api_key);
    }

}