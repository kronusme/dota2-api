<?php
/**
 * Information about game lobbies
 *
 * @author kronus
 * @package data
 * @example
 * <code>
 *   $lobbies = new lobbies();
 *   $lobbies->parse();
 *   $lobbies->get_field_by_id(2, 'name'); // returns 'Tournament'
 * </code>
 */
class lobbies extends data {
    public function __construct() {
        $this->_filename = 'lobbies.json';
        $this->_field = 'lobbies';
    }
}