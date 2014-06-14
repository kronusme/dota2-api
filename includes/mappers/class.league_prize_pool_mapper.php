<?php

/**
 * Common part for league's prize pool mappers (web and db)
 *
 * @author kronus
 * @package mappers
 */
abstract class league_prize_pool_mapper {

    /**
     * @var int
     */
    protected $_league_id;

    /**
     * @var int
     */
    protected $_prize_pool;

    /**
     * @param int $league_id
     * @return league_prize_pool_mapper
     */
    public function set_league_id($league_id) {
        $this->_league_id = intval($league_id);
        return $this;
    }

    /**
     * @return int
     */
    public function get_league_id() {
        return $this->_league_id;
    }

    /**
     * @param int $prize_pool
     * @return league_prize_pool_mapper
     */
    public function set_prize_pool($prize_pool) {
        $this->_prize_pool = intval($prize_pool);
        return $this;
    }

    /**
     * @return int
     */
    public function get_prize_pool() {
        return $this->_prize_pool;
    }

}