<?php

namespace Dota2Api;

use Dota2Api\Utils\Request;

class Api
{

    public static function init($api_key, array $db, $autoselect_db = true)
    {
        $db = call_user_func_array('Dota2Api\Utils\Db::obtain', $db);
        $db->connectPDO($autoselect_db);
        Request::$apiKey = (string)$api_key;
    }
}
