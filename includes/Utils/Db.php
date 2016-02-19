<?php

namespace Dota2Api\Utils;

use PDO;
use PDOException;
use PDOStatement;

/**
 * PHP PDO Database wrapper class using Singleton Pattern
 * Used in applications as a single point of database access if required.
 *
 * @package    database
 * @subpackage
 * @author     Abdul Rashid Gadhwalla <ar.gadhwalla@yahoo.com>
 * @version    1.1 06.06.2012 Improved by Kronu$
 * @todo improve class via replacing 'empty' to 'is_null'
 */
class Db
{

    /**
     * Store the single instance of Database class
     * @static Database $_instance
     */
    private static $_instance;
    /**
     * Database server address
     * @access private
     * @var String $_server
     */
    private $_server = ''; //database server
    /**
     * Database login name
     * @access private
     * @var String $_user
     */
    private $_user = '';
    /**
     * Database login password
     * @access private
     * @var String $_pass
     */
    private $_pass = '';
    /**
     * Database name to be accessed
     * @access private
     * @var String $_database
     */
    private $_database = '';
    /**
     * Database table prefix
     * @access private
     * @var String $_prefix
     * @static
     */
    private static $_prefix = '';
    /**
     * Store error messages for Databases if any
     * @access private
     * @var String $_error
     */
    private $_error = '';
    /**
     * Store database connection
     * @access private
     * @var PDO $_linkId
     * @static
     */
    private static $_linkId;
    /**
     * Represents a prepared statement
     * @access private
     * @var PDOStatement $_queryId
     */
    private $_queryId = 0;

    /**
     * Constructor to initialize the database connection variables
     * @access private
     * @param $server string
     * @param $user string
     * @param $pass string
     * @param $database string
     * @param $pref string
     * @return db
     */
    private function __construct($server = null, $user = null, $pass = null, $database = null, $pref = null)
    {

        if ($server === null || $user === null || $database === null) {
            return;
        }

        $this->_server = $server;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_database = $database;
        self::$_prefix = $pref;
    }

    /**
     * Obtain an instance of Database object
     * @static
     * @param $server string
     * @param $user string
     * @param $pass string
     * @param $database string
     * @param $pref string
     * @return db If object does not exists create new and return else return already created object
     */
    public static function obtain($server = null, $user = null, $pass = null, $database = null, $pref = null)
    {
        if (null === self::$_instance) {
            self::$_instance = new db($server, $user, $pass, $database, $pref);
        }
        return self::$_instance;
    }

    public static function clean()
    {
        self::$_instance = null;
        self::$_linkId = null;
    }

    /**
     * Connect to a Database host and select database using variable initialized above
     * @access public
     * @param bool $selectDb
     * @return bool If Database connection successful return true else return false
     */
    public function connectPDO($selectDb = true)
    {
        if (null !== self::$_linkId) {
            return true;
        }
        try {
            self::$_linkId = new PDO(
                'mysql:host=' . $this->_server,
                $this->_user,
                $this->_pass,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
            );
            self::$_linkId->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if ($selectDb) {
                self::$_linkId->query('USE ' . $this->_database);
            }
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Close a connection to Database host
     * @access public
     */
    public function close()
    {
        self::$_linkId = null;
    }

    /**
     * Prepares and executes a sql query
     * @access private
     * @param String $sql
     * @param Array $params
     * @return Boolean Returns TRUE on success or FALSE on failure.
     */
    private function _queryPDO($sql, array $params = array())
    {
        try {
            $this->_queryId = self::$_linkId->prepare($sql);
            $i = 1;
            foreach ($params as $key => $val) {
                $type = $this->getPDOConstantType($val);
                $this->_queryId->bindValue($i, $val, $type);
                ++$i;
            }
            return $this->_queryId->execute();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
            return false;
        }
    }

    /**
     * Run the query, fetches the first row only, frees resultset
     * @access public
     * @param String $sql
     * @param Array $params
     * @return Mixed The first record as an Array or FALSE in case the query fails to execute.
     */
    public function queryFirstPDO($sql, array $params = array())
    {
        $query_id = $this->_queryPDO($sql, $params);
        if ($query_id === false) {
            return false;
        }
        $out = $this->_fetchPDO();
        $this->_freeResultPDO();
        return $out;
    }

    /**
     * Fetches and returns results one line at a time
     * @access private
     * @return Mixed The first record as an Associative Array or Empty in case if the query_id (i.e. if the query did not execute) is not set.
     */
    private function _fetchPDO()
    {
        return ($this->_queryId === null) ? '' : $this->_queryId->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches and returns all the results (not just one row)
     * @param string $sql
     * @param array $params
     * @return array The complete records as an Associative Array or Empty in case if the query_id (i.e. if the query did not execute) is not set.
     */
    public function fetchArrayPDO($sql, array $params = array())
    {

        $query_id = $this->_queryPDO($sql, $params);
        if ($query_id === false) {
            return false;
        }
        $out = array();

        while ($row = $this->_fetchPDO()) {
            $out[] = $row;
        }

        $this->_freeResultPDO();
        return $out;
    }

    /**
     * Does an update query with an array for data and array as a param for the where clause
     * @access public
     * @param String $table
     * @param Array $data is an assoc array with keys are column names and values as the actual values
     * @param Array $where
     * @return Boolean Returns TRUE on success or FALSE on failure.
     */
    public function updatePDO($table, array $data, array $where = array())
    {

        if (count($data) === 0) {
            return false;
        }
        $q = "UPDATE `$table` SET ";

        foreach ($data as $key => $val) {
            if (null !== $val) {
                $q .= "`$key`=?, ";
            }
        }
        $q = rtrim($q, ', ') . ' WHERE ';

        foreach ($where as $key => $val) {
            if (null !== $val) {
                $q .= "`$key`=? AND ";
            }
        }
        $q = rtrim($q, 'AND ') . ' ;';

        try {
            $this->_queryId = self::$_linkId->prepare($q);

            $i = 1;
            foreach ($data as $key => $val) {
                if (null !== $val) {
                    $type = $this->getPDOConstantType($val);
                    $this->_queryId->bindValue($i, $val, $type);
                    ++$i;
                }
            }


            foreach ($where as $key => $val) {
                $type = $this->getPDOConstantType($val);
                $this->_queryId->bindValue($i, $val, $type);
                ++$i;
            }
            return $this->_queryId->execute();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
            return false;
        }
    }

    /**
     * Insert many data per one query
     * Example: $fields = array('a','b','c') then $data should be array(array(1,2,3), array(4,5,6)) etc
     *
     * @param string $table
     * @param array $fields array of fields names
     * @param array $data array of array of data
     * @param bool $update should data be updated on duplicate
     * @return bool
     */
    public function insertManyPDO($table, array $fields, array $data = array(), $update = false)
    {
        if (count($data) === 0) {
            return false;
        }
        $q = 'INSERT INTO `' . $table . '`';
        $v = '';
        $n = '(`' . implode('`,`', $fields) . '`) ';
        foreach ($data as $sd) {
            $v .= '(';
            $sd_c = count($sd);
            for ($i = 0; $i < $sd_c; $i++) {
                $v .= '?,';
            }
            $v = rtrim($v, ',') . '),';
        }
        $v = rtrim($v, ',');
        $q .= $n . ' VALUES ' . $v;
        if ($update) {
            $q .= ' ON DUPLICATE KEY UPDATE ';
            foreach ($fields as $f) {
                $q .= $f . ' = VALUES(`' . $f . '`),';
            }
            $q = rtrim($q, ',');
        }
        try {
            $this->_queryId = self::$_linkId->prepare($q);

            $i = 1;
            foreach ($data as $sd) {
                foreach ($sd as $val) {
                    $type = $this->getPDOConstantType($val);
                    $this->_queryId->bindValue($i, $val, $type);
                    ++$i;
                }
            }
            $this->_queryId->execute();
            return self::$_linkId->lastInsertId();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
            return false;
        }
    }

    /**
     * Does an insert query with an array for data.
     * @access public
     * @param String $table
     * @param Array $data is an assoc array with keys are column names and values as the actual values
     * @return Mixed Returns ID of the inserted record or FALSE on failure.
     */
    public function insertPDO($table, array $data = array())
    {
        if (count($data) === 0) {
            return false;
        }
        $q = "INSERT INTO `$table` ";
        $v = '';
        $n = '';

        foreach ($data as $key => $val) {
            $n .= "`$key`, ";
            if (strtolower($val) === 'now()') {
                $v .= 'NOW(), ';
                unset($data[$key]);
            } else {
                $v .= '?, ';
            }
        }
        $q .= '(' . rtrim($n, ', ') . ') VALUES (' . rtrim($v, ', ') . ');';
        try {
            $this->_queryId = self::$_linkId->prepare($q);

            $i = 1;
            foreach ($data as $key => $val) {
                $type = $this->getPDOConstantType($val);
                $this->_queryId->bindValue($i, $val, $type);
                ++$i;
            }
            $this->_queryId->execute();
            return self::$_linkId->lastInsertId();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
            return false;
        }

    }

    /**
     * Delete records from table
     * @param string $table
     * @param array $where is an assoc array with keys are column names and values as the actual values
     * @param int $limit max affected rows count (default - 1)
     * @return mixed false on failure
     */
    public function deletePDO($table, array $where = array(), $limit = 1)
    {
        $limit = (int)$limit;
        if (count($where) === 0) {
            return false;
        }
        $q = "DELETE FROM `$table` ";
        $q .= 'WHERE ';
        foreach ($where as $key => $val) {
            if (empty($val)) {
                continue;
            } else {
                $q .= "`$key`=? AND ";
            }
        }
        $q = rtrim($q, 'AND ');
        if ($limit > 0) {
            $q .= ' LIMIT ' . $limit . ' ;';
        } else {
            $q .= ' ;';
        }
        try {
            $this->_queryId = self::$_linkId->prepare($q);
            $i = 1;
            foreach ($where as $key => $val) {
                if (empty($val)) {
                    continue;
                } else {
                    $type = $this->getPDOConstantType($val);
                    $this->_queryId->bindValue($i, $val, $type);
                    ++$i;
                }
            }
            return $this->_queryId->execute();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
            return false;
        }
    }

    /**
     * Checks the data type of the value that is passed
     * @access public
     * @param * $value
     * @return Integer Returns the corresponding PDO ID of the data type.
     */
    public function getPDOConstantType($value)
    {

        switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case (null === $value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
        }
        return $type;
    }

    /**
     * Frees the resultset
     * @access private
     */
    private function _freeResultPDO()
    {
        $this->_queryId->closeCursor();
    }

    /**
     * Return the last error message that is set.
     * @access public
     * @return string Return the value stored in 'error' property of the class
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Return table name with prefix
     * @access public
     * @param $tbl string
     * @return string Real table name
     */
    public static function realTablename($tbl)
    {
        return self::$_prefix . (string)$tbl;
    }

    public function exec($query)
    {
        return self::$_linkId->exec($query);
    }
}
