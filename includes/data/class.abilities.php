<?php
/**
 * Information about heroes abilities
 *
 * @author kronus
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
class abilities extends heroes_data {
    /**
     * Stats ability identifier
     */
    const stats_ability_id = 5002;

    public function __construct() {
        $this->set_filename('abilities.json');
        $this->set_field('abilities');
        // no small images for abilities :(
        $this->_suffixes['thumb'] = 'lg';
    }

    public function get_img_url_by_id($id, $thumb = true) {
        if ($id != self::stats_ability_id) {
            return parent::get_img_url_by_id($id, $thumb);
        }
        return 'images/stats.png';
    }
}