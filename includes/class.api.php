<?php

class api {

    public static function init($api_key, array $db, $autoselect_db = true) {
        $db = call_user_func_array('db::obtain', $db);
        $db->connect_pdo($autoselect_db);
        request::$api_key = strval($api_key);
    }

}