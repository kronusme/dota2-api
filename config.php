<?php

require('api-key.php');

/**
 * Basic class with system's configuration data
 */
class config {
    /**
     * Configuration data
     * @access private
     * @static
     * @var array
     */
    private static $_data = array(
        'db_user' => 'root',
        'db_pass' => '',
        'db_host' => 'localhost',
        'db_name' => 'dota2_dev_db',
        'db_table_prefix' => ''
    );

    /**
     * Private construct to avoid object initializing
     * @access private
     */
    private function __construct() {}

    public static function init($select_db) {
        $db = db::obtain(self::get('db_host'), self::get('db_user'), self::get('db_pass'), self::get('db_name'), self::get('db_table_prefix'));
        if (!$db->connect_pdo($select_db)) {
            echo $db->get_error();
            die('Unable to connect to MySQL Server!');
        }
    }

    /**
     * Get configuration parameter by key
     * @param string $key data-array key
     * @return mixed|null
     */
    public static function get($key) {
        if(isset(self::$_data[$key])) {
            return self::$_data[$key];
        }
        return null;
    }
}

config::init(!defined('testing'));
