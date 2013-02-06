<?php
/**
 *
 */
class match extends stat_object {
    /**
     * @var int
     */
    protected $_match_id;
    /**
     * @var string
     */
    protected $_season;
    /**
     * @var bool
     */
    protected $_radiant_win;
    /**
     * @var int
     */
    protected $_duration;
    /**
     * @var int
     */
    protected $_first_blood_time;
    /**
     * @var timestamp
     */
    protected $_start_time;
    /**
     * @var int
     */
    protected $_game_mode;
    /**
     * @var int
     */
    protected $_tower_status_radiant;
    /**
     * @var int
     */
    protected $_tower_status_dire;
    /**
     * @var int
     */
    protected $_barracks_status_radiant;
    /**
     * @var int
     */
    protected $_barracks_status_dire;
    /**
     * @var int
     */
    protected $_replay_salt;
    /**
     * @var int
     */
    protected $_lobby_type;
    /**
     * @var int
     */
    protected $_human_players;
    /**
     * @var int
     */
    protected $_leagueid;
    /**
     * @var int
     */
    protected $_cluster;
    /**
     * @var int
     */
    protected $_positive_votes;
    /**
     * @var int
     */
    protected $_negative_votes;
    /**
     * @var array
     */
    protected $_slots = array();
    /**
     * @param slot $slot
     * @return match
     */
    public function add_slot(slot $slot) {
        $this->_slots[$slot->get('player_slot')] = $slot;
        return $this;
    }
    /**
     * @param int $index
     * @return slot | null
     */
    public function get_slot($index) {
        $index = intval($index);
        if (isset($this->_slots[$index])) {
            return $this->_slots[$index];
        }
        return null;
    }

    /**
     * @return array
     */
    public function get_all_slots() {
        return $this->_slots;
    }
    /**
     *
     */
    public function __construct() {

    }

}
