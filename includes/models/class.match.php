<?php
/**
 * All info about match including picks, bans (if cm-mode), ability upgrades (if provided) and slots
 *
 * Don't create match-objects via $match = new match(); !
 * Use mappers for this
 * @uses slot
 * @author kronus
 * @package models
 */
class match extends stat_object {
    /**
     * Identify cm-mode
     */
    const CAPTAINS_MODE = 2;
    /**
     * the numeric match ID
     * @var int
     */
    protected $_match_id;
    /**
     * @var string
     */
    protected $_season;
    /**
     * true if radiant won, false otherwise
     * @var bool
     */
    protected $_radiant_win;
    /**
     * the total time in seconds the match ran for
     * @var int
     */
    protected $_duration;
    /**
     * the time in seconds at which first blood occurred
     * @var int
     */
    protected $_first_blood_time;
    /**
     * date in UTC seconds since Jan 1, 1970 (unix time format)
     * @var timestamp
     */
    protected $_start_time;
    /**
     * the match's sequence number - the order in which matches are recorded
     * @var int
     */
    protected $_match_seq_num;
    /**
     * a number representing the game mode of this match
     * @var int
     */
    protected $_game_mode;
    /**
     * an 11-bit unsigned int
     * @var int
     */
    protected $_tower_status_radiant;
    /**
     * an 11-bit unsigned int
     * @var int
     */
    protected $_tower_status_dire;
    /**
     * a 6-bit unsigned int
     * @var int
     */
    protected $_barracks_status_radiant;
    /**
     * a 6-bit unsigned int
     * @var int
     */
    protected $_barracks_status_dire;
    /**
     * @var int
     */
    protected $_replay_salt;
    /**
     * the type of lobby
     * @var int
     */
    protected $_lobby_type;
    /**
     * the number of human players in the match
     * @var int
     */
    protected $_human_players;
    /**
     * the leauge this match is from
     * @var int
     */
    protected $_leagueid;
    /**
     * @var int
     */
    protected $_cluster;
    /**
     * the number of thumbs up the game has received
     * @var int
     */
    protected $_positive_votes;
    /**
     * the number of thumbs up the game has received
     * @var int
     */
    protected $_negative_votes;
    /**
     * @var int
     */
    protected $_radiant_team_id;
    /**
     * the name of the radiant team
     * @var string
     */
    protected $_radiant_name;
    /**
     * the radiant team's logo
     * @var string
     */
    protected $_radiant_logo;
    /**
     * radiant_team_complete - true if all players on radiant belong to this team, false otherwise (i.e. are the stand-ins {false} or not {true})
     * @var bool
     */
    protected $_radiant_team_complete;
    /**
     * @var int
     */
    protected $_dire_team_id;
    /**
     * the name of the dire team
     * @var string
     */
    protected $_dire_name;
    /**
     * the dire team's logo
     * @var string
     */
    protected $_dire_logo;
    /**
     * true if all players on dire belong to this team, false otherwise (i.e. are the stand-ins {false} or not {true})
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
     * Return array of all slots divided by team (5 for radiant, 5 for dire)
     * @return array
     */
    public function get_all_slots_divided() {
        $return = array('radiant' => array(), 'dire' => array());
        foreach($this->_slots as $slot) {
            $team = 'radiant';
            if ($slot->get('player_slot') > 5) {
                $team = 'dire';
            }
            array_push($return[$team], $slot);
        }
        return $return;
    }

    /**
     * Return array of all picks and bans
     * @return array
     */
    public function get_all_picks_bans() {
        return $this->_picks_bans;
    }

    /**
     * Return array of all picks and bans divided by team (and then by bans and picks)
     * @return array
     */
    public function get_all_picks_bans_divided() {
        $return = array(
            'radiant' => array(
                'bans' => array(),
                'picks' => array()
            ),
            'dire' => array(
                'bans' => array(),
                'picks' => array()
            )
        );
        foreach($this->_picks_bans as $pick_ban) {
            $team = 'radiant';
            $state = 'picks';
            if ($pick_ban['team']) {
                $team = 'dire';
            }
            if (!$pick_ban['is_pick']) {
                $state = 'bans';
            }
            array_push($return[$team][$state], $pick_ban);
        }
        return $return;
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
