<?php

namespace Dota2Api;

class Api {

    public static function init($api_key, array $db, $autoselect_db = true) {
        $db = call_user_func_array('Dota2Api\Utils\Db::obtain', $db);
        $db->connect_pdo($autoselect_db);
        Utils\Request::$api_key = strval($api_key);
    }

}