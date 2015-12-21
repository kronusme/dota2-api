<?php

namespace Dota2Api\Data;

/**
 * Basic functionality for JSON file processing (with data like abilities, heroes etc)
 *
 * @author kronus
 */
abstract class Data
{
    /**
     * Folder with json files
     */
    const PATH = 'data';
    /**
     * JSON filename
     * @var string
     */
    protected $_filename;

    /**
     * Parsed data
     * Format - array(id=>array, id=>array, ...)
     * @var array
     */
    protected $_data = array();

    /**
     * Field name in the JSON file
     * @var string
     */
    protected $_field;

    /**
     * Suffixes for images names
     * @var array
     */
    protected $_suffixes = array('thumb' => 'eg', 'large' => 'lg');

    /**
     * @param string $field
     * @return data
     */
    public function setField($field)
    {
        $this->_field = (string)$field;
        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * @param array $data
     * @return data
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param string $filename
     * @return data
     */
    public function setFilename($filename)
    {
        $this->_filename = (string)$filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Parse JSON file
     * @param string $parseTo
     */
    public function parse($parseTo = '')
    {
        $parseTo = str_replace('.', '', $parseTo); // allow to use '6.86' and '686'
        $p = __DIR__ . '/../../' . self::PATH . '/';
        $fullpath = $p . $this->_filename;
        $initData = $this->_parseJsonFile($fullpath);
        if ($parseTo) {
            $subdirs = scandir($p);
            sort($subdirs);
            foreach ($subdirs as $sdir) {
                $subdir = $p . $sdir;
                if (!is_dir($subdir) || $sdir === '.' || $sdir === '..') {
                    continue;
                }
                if ($sdir > $parseTo) {
                    break;
                }
                $path = $subdir . '/' . $this->_filename;
                if (file_exists($path)) {
                    $patchData = $this->_parseJsonFile($path);
                    $initData = $this->_mergeById($initData, $patchData);
                }
            }
        }
        $this->_data = $initData;
    }

    protected function _mergeById($arr1, $arr2)
    {
        foreach ($arr2 as $k => $row) {
            $arr1[$k] = $row;
        }
        return $arr1;
    }

    protected function _parseJsonFile($fullpath)
    {
        $return = array();
        if (file_exists($fullpath)) {
            $content = file_get_contents($fullpath);
            $data = json_decode($content);
            $field = $this->getField();
            if ($field && $data->$field) {
                foreach ($data->$field as $obj) {
                    $obj_array = (array)$obj;
                    $return[$obj_array['id']] = $obj_array;
                }
            }
        }
        return $return;
    }

    /**
     * Get array of data by its id (data should be parsed before)
     *
     * @param int $id
     * @return array|null
     */
    public function getDataById($id)
    {
        $id = (int)$id;
        return array_key_exists($id, $this->_data) ? $this->_data[$id] : null;
    }

    /**
     * Get some field ($field_name) value in the data with id = $id
     * @param int $id
     * @param string $field_name
     * @return mixed|null
     */
    public function getFieldById($id, $field_name)
    {
        $data = $this->getDataById($id);
        if (null !== $data) {
            $field_name = (string)$field_name;
            if (array_key_exists($field_name, $data)) {
                return $data[$field_name];
            }
        }
        return null;
    }
}
