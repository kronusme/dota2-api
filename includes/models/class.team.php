<?php
/**
 *
 */
class team extends stat_object {
    /**
     * @var int
     */
    protected $_team_id;
    /**
     * @var string
     */
    protected $_name;
    /**
     * @var string
     */
    protected $_tag;
    /**
     * @var timestamp
     */
    protected $_time_created;
    /**
     * @var int
     */
    protected $_rating;
    /**
     * @var string
     */
    protected $_logo;
    /**
     * @var string
     */
    protected $_logo_sponsor;
    /**
     * @var string
     */
    protected $_country_code;
    /**
     * @var int
     */
    protected $_games_played_with_current_roster;
    /**
     * @var int
     */
    protected $_admin_account_id;
    /**
     * @var string
     */
    protected $_url;
    /**
     * @var array
     */
    protected $_players_ids = array();
    /**
     * @var array
     */
    protected $_leagues_ids = array();

    /**
     * @return array
     */
    public function get_all_players_ids() {
        return $this->_players_ids;
    }

    /**
     * @param array $ids
     * @return team
     */
    public function set_all_players_ids(array $ids) {
        foreach($ids as $id) {
            $this->add_player_id($id);
        }
        return $this;
    }

    /**
     * @param int $index
     * @return int|null
     */
    public function get_player_id($index) {
        $index = intval($index);
        if (isset($this->_players_ids[$index])) {
            return $this->_players_ids[$index];
        }
        return null;
    }

    /**
     * @param int $player_id
     * @return team
     */
    public function add_player_id($player_id) {
        $player_id = intval($player_id);
        array_push($this->_players_ids, $player_id);
        return $this;
    }

    /**
     * @return array
     */
    public function get_all_leagues_ids() {
        return $this->_leagues_ids;
    }

    /**
     * @param array $leagues
     * @return team
     */
    public function set_all_leagues_ids(array $leagues) {
        foreach($leagues as $id) {
            $this->add_league_id($id);
        }
        return $this;
    }

    /**
     * @param int $index
     * @return int|null
     */
    public function get_leagues_id($index) {
        $index = intval($index);
        if (isset($this->_leagues_ids[$index])) {
            return $this->_leagues_ids[$index];
        }
        return null;
    }

    /**
     * @param int $league_id
     * @return team
     */
    public function add_league_id($league_id) {
        $league_id = intval($league_id);
        array_push($this->_leagues_ids, $league_id);
        return $this;
    }

    /**
     *
     */
    public function __construct(){}
}
