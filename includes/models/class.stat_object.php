<?php
/**
 *
 */
abstract class stat_object {
    /**
     * Get field value
     * @param string $name
     * @return mixed | null
     */
    public function get($name) {
        $name = '_'.(string)$name;
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
    /**
     * Set field value
     * @param string $name
     * @param mixed $value
     * @return stat_object
     */
    public function set($name, $value) {
        $name = '_'.(string)$name;
        if (property_exists($this, $name) && (!is_array($this->$name))) {
            $this->$name = $value;
        }
        return $this;
    }

    /**
     * @param array $data
     * @return stat_object
     */
    public function set_array(array $data) {
        foreach($data as $name=>$value) {
            $this->set($name, $value);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function get_data_array() {
        $data = get_object_vars($this);
        $ret = array();
        foreach($data as $key => $value) {
            if (!is_array($value)) {
                $ret[ltrim($key, '_')] = $value;
            }
        }
        return $ret;
    }
}
