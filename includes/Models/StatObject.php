<?php

namespace Dota2Api\Models;

/**
 * Basic class for all statistic objects used in system
 * Provide pure access for fields (with basic types!)
 *
 * @author kronus
 */
abstract class StatObject
{
    /**
     * Get field value
     * @param string $name
     * @return mixed | null
     */
    public function get($name)
    {
        $name = '_' . (string)$name;
        return property_exists($this, $name) ? $this->$name : null;
    }

    /**
     * Set field value (if field isn't array)
     * @param string $name
     * @param mixed $value
     * @return StatObject
     */
    public function set($name, $value)
    {
        $name = '_' . (string)$name;
        if (property_exists($this, $name) && (!is_array($this->$name))) {
            $this->$name = $value;
        }
        return $this;
    }

    /**
     * Set not-array fields
     * @param array $data
     * @return StatObject
     */
    public function setArray(array $data)
    {
        foreach ($data as $name => $value) {
            $this->set($name, $value);
        }
        return $this;
    }

    /**
     * Return all not-array fields as assoc array
     * @return array
     */
    public function getDataArray()
    {
        $data = get_object_vars($this);
        $ret = array();
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $ret[ltrim($key, '_')] = $value;
            }
        }
        return $ret;
    }
}
