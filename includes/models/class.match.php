<?php
/**
 * All info about match including picks, bans (if cm-mode), ability upgrades (if provided) and slots
 *
 * Don't create match-objects via $match = new match(); !
 * Use mappers for this
 * @used slot
 */
class match extends stat_object {
    /**
     * Identify cm-mode
     */
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
     * Array of slot objects (should be 10)
     * @var array
     */
    protected $_slots = array();
    /**
     * Array of picks and bans info (not empty if game-mode is CM)
     * @var array
     */
    protected $_picks_bans = array();
    /**
     * Add new slot for slots-array
     * @param slot $slot
     * @return match
     */
    public function add_slot(slot $slot) {
        $this->_slots[$slot->get('player_slot')] = $slot;
        return $this;
    }
    /**
     * Get slot by its index (0-4 for radiant, 128-132 for dire - as it is in the field player_slot)
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
     * Return array of all slots
     * @return array
     */
    public function get_all_slots() {
        return $this->_slots;
    }

    /**
     * Return array of all picks and bans
     * @return array
     */
    public function get_all_picks_bans() {
        return $this->_picks_bans;
    }

    /**
     * Set whole array of picks, bans.
     * Used in mappers
     *
     * @param array $data
     * @return match
     */
    public function set_all_pick_bans(array $data) {
        $this->_picks_bans = $data;
        return $this;
    }
    /**
     * Just empty construct.
     * Don't use me directly!
     */
    public function __construct() {

    }

}
