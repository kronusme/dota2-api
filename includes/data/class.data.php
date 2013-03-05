<?php
/**
 * Basic functionality for JSON file processing (with data like abilities, heroes etc)
 *
 * @author kronus
 * @package data
 */
abstract class data {
    /**
     * Folder with json files
     */
    const path = 'data';
    /**
     * JSON filename
     * @var string
     */
    private $_filename;

    /**
     * Parsed data
     * @var array
     */
    private $_data = array();

    /**
     * Field name in the JSON file
     * @var string
     */
    private $_field;

    /**
     * @param string $field
     * @return data
     */
    public function set_field($field) {
        $this->_field = (string)$field;
        return $this;
    }

    /**
     * @return string
     */
    public function get_field() {
        return $this->_field;
    }

    /**
     * @param array $data
     * @return data
     */
    public function set_data(array $data) {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function get_data() {
        return $this->_data;
    }

    /**
     * @param string $filename
     * @return data
     */
    public function set_filename($filename) {
        $this->_filename = (string)$filename;
        return $this;
    }

    /**
     * @return string
     */
    public function get_filename() {
        return $this->_filename;
    }

    /**
     * Parse JSON file
     */
    public function parse() {
        $fullpath = str_replace('includes', '', config::get('base_path')).self::path.DIRECTORY_SEPARATOR.$this->_filename;
        if (file_exists($fullpath)) {
            $content = file_get_contents($fullpath);
            $data = json_decode($content);
            $return = array();
            $field = $this->get_field();
            if ($field && $data->$field) {
                foreach($data->$field as $obj) {
                    $obj_array = (array)$obj;
                    $return[$obj_array['id']] = $obj_array;
                }
                $this->_data = $return;
            }
        }
    }
}
