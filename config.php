<?php

require('api-key.php');

//The language to retrieve results in (see http://en.wikipedia.org/wiki/ISO_639-1 for the language codes (first two characters) and http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes for the country codes (last two characters))
define ('LANGUAGE', 'en_us');

error_reporting(E_ALL);

set_time_limit(0);

class AutoLoader {

    static private $classNames = array();

    /**
     * Store the filename (sans extension) & full path of all ".php" files found
     */
    public static function registerDirectory($dirName) {

        $di = new DirectoryIterator($dirName);
        foreach ($di as $file) {

            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                // recurse into directories other than a few special ones
                self::registerDirectory($file->getPathname());
            }
            else {
                if (substr($file->getFilename(), -4) === '.php' && substr($file->getFilename(), 0, 6) === 'class.') {
                    // save the class name / path of a .php file found
                    $className = str_replace(array('class.', '.php'), '', $file->getFilename());
                    self::registerClass($className, $file->getPathname());
                }
            }
        }
    }

    public static function registerClass($className, $fileName) {
        self::$classNames[$className] = $fileName;
    }

    public static function loadClass($className) {
        if (isset(self::$classNames[$className])) {
            require_once(self::$classNames[$className]);
        }
    }

}

spl_autoload_register(array('AutoLoader', 'loadClass'));

AutoLoader::registerDirectory(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes');
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
        self::$_data['base_path'] = dirname(__FILE__).DIRECTORY_SEPARATOR.'includes';
        $db = db::obtain(self::get('db_host'), self::get('db_user'), self::get('db_pass'), self::get('db_name'), self::get('db_table_prefix'));
        if (!$db->connect_pdo($select_db)) {
            echo $db->get_error();
            die('Unable to connect to MySQL Server!');
        }
    }
    /**
     * Get configuration parameter by key
     * @param string $key data-array key
     * @return null
     */
    public static function get($key) {
        if(isset(self::$_data[$key])) {
            return self::$_data[$key];
        }
        return null;
    }
}

config::init(!defined('testing'));
