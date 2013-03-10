<?php
/**
 * Information about game modes
 *
 * @author kronus
 * @package data
 * @example
 * <code>
 *   $mods = new mods();
 *   $mods->parse();
 *   $mods->get_field_by_id(1, 'name'); // returns 'All Pick'
 * </code>
 */
class mods extends data {
    public function __construct() {
        $this->_filename = 'mods.json';
        $this->_field = 'mods';
    }
}
