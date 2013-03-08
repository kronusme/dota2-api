<?php
/**
 * Information about heroes abilities
 *
 * @author KronuS
 * @package data
 * @example
 * <code>
 *   $abilities = new abilities();
 *   $abilities->parse();
 *   $abilities-get_data_by_id(5172); // return array for ability with id 5172 (BeastMaster Inner Beast)
 *   // same, because there are no thumbs for abilities
 *   $abilities->get_img_url_by_id(5172, false);
 *   $abilities->get_img_url_by_id(5172);
 * </code>
 */
class abilities extends data {
    public function __construct() {
        $this->set_filename('abilities.json');
        $this->set_field('abilities');
        // no small images for abilities :(
        $this->_suffixes['thumb'] = 'lg';
    }
}