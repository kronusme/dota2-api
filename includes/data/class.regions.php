<?php
/**
 * Information about servers regions
 *
 * @author kronus
 * @package data
 * @example
 * <code>
 *   $regions = new regions();
 *   $regions->parse();
 *   $regions->get_field_by_id(132, 'name'); // returns 'Europe West'
 * </code>
 */
class regions extends data {
    public function __construct() {
        $this->_filename = 'regions.json';
        $this->_field = 'regions';
    }
}