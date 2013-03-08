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
     * Format - array(id=>array, id=>array, ...)
     * @var array
     */
    private $_data = array();

    /**
     * Field name in the JSON file
     * @var string
     */
    private $_field;

    /**
     * Suffixes for images names
     * @var array
     */
    private $_suffixes = array('thumb' => 'eg', 'large' => 'lg');

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

    /**
     * Get array of data by its id (data should be parsed before)
     *
     * @param int $id
     * @return array|null
     */
    public function get_data_by_id($id) {
        $id = intval($id);
        if (isset($this->_data[$id])) {
            return $this->_data[$id];
        }
        return null;
    }

    /**
     * Generate image url (uses steam servers)
     *
     * @param int $id item identifier
     * @param bool $thumb return small or large image url
     * @return string
     */
    public function get_img_url_by_id($id, $thumb = true) {
        $id = intval($id);
        $data = $this->get_data_by_id($id);
        if (is_null($data)) {
            return '';
        }
        else {
            $suffix = $thumb ? $this->_suffixes['thumb']: $this->_suffixes['large'];
            return 'http://media.steampowered.com/apps/dota2/images/'.$this->_field.'/'.$data['name'].'_'.$suffix.'.png';
        }
    }
}
