<?php

namespace Dota2Api\Mappers;


/**
 * Common part for match mappers (web and db)
 *
 * @author kronus
 * @package mappers
 */
abstract class MatchMapper {
    /**
     * @var int
     */
    private $_match_id;
    /**
     * @param int $match_id
     * @return MatchMapper
     */
    public function set_match_id($match_id) {
        $this->_match_id = intval($match_id);
        return $this;
    }
    /**
     * @return int
     */
    public function get_match_id() {
        return $this->_match_id;
    }

    /**
     *
     */
    public function __construct($match_id) {
        $this->set_match_id($match_id);
    }
    /**
     * Load info by match_id
     * @return mixed
     */
    abstract public function load();
}
