<?php
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


class db{

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
     * @var PDO $_link_id
     * @static
     */
    private static $_link_id = null;
    /**
     * Represents a prepared statement
     * @access private
     * @var PDOStatement $_query_id
     */
    private $_query_id = 0;

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
    private function __construct($server=null, $user=null, $pass=null, $database=null, $pref=null){

        if($server==null || $user==null || $database==null)
        {
            return;
        }

        $this->_server=$server;
        $this->_user=$user;
        $this->_pass=$pass;
        $this->_database=$database;
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
    public static function obtain($server=null, $user=null, $pass=null, $database=null, $pref=null){
        if (is_null(self::$_instance)){
            self::$_instance = new db($server, $user, $pass, $database, $pref);
        }
        return self::$_instance;
    }
    /**
     * Connect to a Database host and select database using variable initialized above
     * @access public
     * @return bool If Database connection successful return true else return false
     */
    public function connect_pdo()
    {
        if (!is_null(self::$_link_id)) { return true; }
        try
        {
            self::$_link_id = new PDO('mysql:host='.$this->_server.';dbname='.$this->_database.'', $this->_user, $this->_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            self::$_link_id->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            $this->_error = $e->getMessage();
            return false;
        }
        return true;
    }
    /**
     * Close a connection to Database host
     * @access public
     */
    public function close(){
        self::$_link_id = null;
    }
    /**
     * Prepares and executes a sql query
     * @access private
     * @param String $sql
     * @param Array $params
     * @return Boolean Returns TRUE on success or FALSE on failure.
     */
    private function _query_pdo($sql,$params=array()) {
        try
        {
            $this->_query_id = self::$_link_id->prepare($sql);
            $i=1;
            foreach($params as $key=>$val) {

                $type = $this->getPDOConstantType($val);
                $this->_query_id->bindValue($i, $val, $type);
                ++$i;
            }
            return $this->_query_id->execute();
        }
        catch(PDOException $e)
        {
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
    public function query_first_pdo($sql,$params=array()){
        $query_id = $this->_query_pdo($sql,$params);
        if($query_id === false)
            return false;
        $out = $this->_fetch_pdo();
        $this->_free_result_pdo();
        return $out;
    }
    /**
     * Fetches and returns results one line at a time
     * @access private
     * @return Mixed The first record as an Associative Array or Empty in case if the query_id (i.e. if the query did not execute) is not set.
     */
    private function _fetch_pdo(){

        $record = "";
        if (isset($this->_query_id)){
            $record = $this->_query_id->fetch(PDO::FETCH_ASSOC);
        }
        return $record;
    }
    /**
     * Fetches and returns all the results (not just one row)
     * @access public
     * @param String $sql
     * @param Array $params
     * @return Mixed The complete records as an Associative Array or Empty in case if the query_id (i.e. if the query did not execute) is not set.
     */
    public function fetch_array_pdo($sql,$params = array()){

        $query_id = $this->_query_pdo($sql,$params);
        if($query_id  === false)
            return false;
        $out = array();

        while ($row = $this->_fetch_pdo()){

            $out[] = $row;
        }

        $this->_free_result_pdo();
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
    public function update_pdo($table, $data, $where=array()){

        if(empty($data))
            return false;
        $q="UPDATE `$table` SET ";

        foreach($data as $key=>$val){
            if(is_null($val));            // fix by KronuS 07.12.2012. Was "if(empty($val));"
            else $q.= "`$key`=?, ";
        }
        $q = rtrim($q, ', ') . ' WHERE ';

        foreach($where as $key=>$val)
        {
            if(empty($val));
            else $q.= "`$key`=? AND ";
        }
        $q = rtrim($q, 'AND ') . ' ;';

        try
        {

            $this->_query_id = self::$_link_id->prepare($q);

            $i=1;
            foreach($data as $key=>$val)
            {
                if(is_null($val))       // fix by KronuS 07.12.2012. Was "if(empty($val));"
                    ;
                else
                {
                    $type = $this->getPDOConstantType($val);
                    $this->_query_id->bindValue($i, $val, $type);
                    ++$i;
                }
            }


            foreach($where as $key=>$val) {

                $type = $this->getPDOConstantType($val);
                $this->_query_id->bindValue($i, $val, $type);
                ++$i;
            }
            return $this->_query_id->execute();
        }
        catch(PDOException $e)
        {
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
     * @return bool
     */
    public function insert_many_pdo($table, array $fields, array $data = array()) {
        if (empty($data)) {
            return false;
        }
        $q = 'INSERT INTO `'.$table.'`';
        $v = '';
        $n = '(`'.implode('`,`', $fields).'`) ';
        foreach($data as $sd) {
            $v .= '(';
            foreach ($sd as $val) {
                $v .= '?,';
            }
            $v = rtrim($v, ',').'),';
        }
        $v = rtrim($v, ',');
        $q .= $n . ' VALUES '.$v;
        try
        {
            $this->_query_id = self::$_link_id->prepare($q);

            $i=1;
            foreach($data as $sd) {
                foreach($sd as $val){
                    $type = $this->getPDOConstantType($val);
                    $this->_query_id->bindValue($i, $val, $type);
                    ++$i;
                }
            }
            $this->_query_id->execute();
            return self::$_link_id->lastInsertId();
        }
        catch(PDOException $e)
        {
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
    public function insert_pdo($table, $data=array())
    {
        if(empty($data))
            return false;
        $q="INSERT INTO `$table` ";
        $v=''; $n='';

        foreach($data as $key=>$val){
            $n.="`$key`, ";
            if(strtolower($val)=='now()')
            {
                $v.="NOW(), ";
                unset($data[$key]);
            }
            else
                $v.= "?, ";
        }
        $q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
        try
        {
            $this->_query_id = self::$_link_id->prepare($q);

            $i=1;
            foreach($data as $key=>$val) {

                $type = $this->getPDOConstantType($val);
                $this->_query_id->bindValue($i, $val, $type);
                ++$i;
            }
            $this->_query_id->execute();
            return self::$_link_id->lastInsertId();
        }
        catch(PDOException $e)
        {
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
    public function delete_pdo($table, array $where = array(), $limit = 1) {
        $limit = intval($limit);
        if(empty($where))
            return false;
        $q ="DELETE FROM `$table` ";
        $q .= 'WHERE ';
        foreach($where as $key=>$val)
        {
            if(empty($val)) continue;
            else $q.= "`$key`=? AND ";
        }
        $q = rtrim($q, 'AND ');
        if ($limit > 0) {
            $q .= ' LIMIT '.$limit. ' ;';
        }
        else {
            $q .= ' ;';
        }
        try
        {
            $this->_query_id = self::$_link_id->prepare($q);
            $i = 1;
            foreach($where as $key=>$val)
            {
                if(empty($val)) continue;
                else
                {
                    $type = $this->getPDOConstantType($val);
                    $this->_query_id->bindValue($i, $val, $type);
                    ++$i;
                }
            }
            return $this->_query_id->execute();
        }
        catch(PDOException $e)
        {
            $this->_error = $e->getMessage();
            return false;
        }
    }
    /**
     * Checks the data type of the value that is passed
     * @access public
     * @param Mixed $value
     * @return Integer Returns the corresponding PDO ID of the data type.
     */
    public function getPDOConstantType($value)
    {

        switch (true)
        {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
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
    private function _free_result_pdo() {
        $this->_query_id->closeCursor();
    }
    /**
     * Return the last error message that is set.
     * @access public
     * @return string Return the value stored in 'error' property of the class
     */
    public function get_error()
    {
        return $this->_error;
    }

    /**
     * Return table name with prefix
     * @access public
     * @param $tbl string
     * @return string Real table name
     */
    public static function real_tablename($tbl) {
        return self::$_prefix.(string)$tbl;
    }

    public function exec($query) {
        return self::$_link_id->exec($query);
    }
}