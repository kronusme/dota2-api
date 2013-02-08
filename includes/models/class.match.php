<?php
/**
 *
 */
class match extends stat_object {
    const CAPTAINS_MODE = 2;
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
     * @var int
     */
    protected $_radiant_team_id;
    /**
     * @var string
     */
    protected $_radiant_name;
    /**
     * @var string
     */
    protected $_radiant_logo;
    /**
     * @var bool
     */
    protected $_radiant_team_complete;
    /**
     * @var int
     */
    protected $_dire_team_id;
    /**
     * @var string
     */
    protected $_dire_name;
    /**
     * @var string
     */
    protected $_dire_logo;
    /**
     * @var bool
     */
    protected $_dire_team_complete;
    /**
     * @var array
     */
    protected $_slots = array();
    /**
     * @var array
     */
    protected $_picks_bans = array();
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
     * @return array
     */
    public function get_all_picks_bans() {
        return $this->_picks_bans;
    }

    /**
     * @param array $data
     * @return match
     */
    public function set_all_pick_bans(array $data) {
        $this->_picks_bans = $data;
        return $this;
    }
    /**
     *
     */
    public function __construct() {

    }

}
