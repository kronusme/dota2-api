<?php
/**
 * Load info about matches from web
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $matches_mapper_web = new matches_mapper_web();
 *   $matches_mapper_web->set_account_id(93712171);
 *   $matches_short_info = $matches_mapper_web->load();
 *   foreach ($matches_short_info AS $key=>$match_short_info) {
 *     $match_mapper = new match_mapper_web($key);
 *     $match = $match_mapper->load();
 *     $mm = new match_mapper_db();
 *     $mm->save($match);
 *   }
 * </code>
 */
class matches_mapper_web {
    const steam_matches_url = 'https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/';
    /**
     * Search matches with a player name, exact match only
     * @var string
     */
    private $_player_name;
    /**
     * Search for matches with a specific hero being played (hero ID, not name)
     * @var int
     */
    private $_hero_id;
    /**
     * 0 for any, 1 for normal, 2 for high, 3 for very high skill (default is 0)
     * @var int
     */
    private $_skill;
    /**
     * date in UTC seconds since Jan 1, 1970 (unix time format)
     * @var timestamp
     */
    private $_date_min;
    /**
     * date in UTC seconds since Jan 1, 1970 (unix time format)
     * @var timestamp
     */
    private $_date_max;
    /**
     * A user's 32-bit steam ID
     * @var int
     */
    private $_account_id;
    /**
     * matches for a particular league
     * @var int
     */
    private $_league_id;
    /**
     * Start the search at the indicated match id, descending
     * @var int
     */
    private $_start_at_match_id;
    /**
     * Maximum is 25 matches (default is 25)
     * @var int
     */
    private $_matches_requested;

    /**
     * set to only show tournament games
     * @var string
     */
    private $_tournament_games_only;

    /**
     * @param string $name
     * @return matches_mapper_web
     */
    public function set_player_name($name) {
        $this->_player_name = (string)$name;
        return $this;
    }

    /**
     * @return string | null
     */
    public function get_player_name() {
        return $this->_player_name;
    }

    /**
     * @param int $hero_id
     * @return matches_mapper_web
     */
    public function set_hero_id($hero_id) {
        $this->_hero_id = intval($hero_id);
        return $this;
    }

    /**
     * @return int
     */
    public function get_hero_id() {
        return $this->_hero_id;
    }

    /**
     * @param int $skill
     * @return matches_mapper_web
     */
    public function set_skill($skill) {
        $skill = intval($skill);
        if ($skill >= 0 && $skill <= 3) {
            $this->_skill = $skill;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function get_skill() {
        return $this->_skill;
    }

    /**
     * @param int $timestamp
     * @return matches_mapper_web
     */
    public function set_date_max($timestamp) {
        $timestamp = intval($timestamp);
        if ($timestamp >= 0 && $timestamp < 2147483647) {
            $this->_date_max = $timestamp;
        }
        return $this;
    }

    /**
     * @return timestamp
     */
    public function get_date_max() {
        return $this->_date_max;
    }

    /**
     * @param int $timestamp
     * @return matches_mapper_web
     */
    public function set_date_min($timestamp) {
        $timestamp = intval($timestamp);
        if ($timestamp >= 0 && $timestamp < 2147483647) {
            $this->_date_min = $timestamp;
        }
        return $this;
    }

    /**
     * @return timestamp
     */
    public function get_date_min() {
        return $this->_date_min;
    }

    /**
     * @param int $account_id
     * @return matches_mapper_web
     */
    public function set_account_id($account_id) {
        $this->_account_id = intval($account_id);
        return $this;
    }

    /**
     * @return int
     */
    public function get_account_id() {
        return $this->_account_id;
    }

    /**
     * @param int $league_id
     * @return matches_mapper_web
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
     * @param int $match_id
     * @return matches_mapper_web
     */
    public function set_start_at_match_id($match_id) {
        $this->_start_at_match_id = intval($match_id);
        return $this;
    }

    /**
     * @return int
     */
    public function get_start_at_match_id() {
        return $this->_start_at_match_id;
    }

    /**
     * @param int $matches_requested
     * @return matches_mapper_web
     */
    public function set_matches_requested($matches_requested) {
        $matches_requested = intval($matches_requested);
        if ($matches_requested > 0 && $matches_requested <= 25) {
            $this->_matches_requested = $matches_requested;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function get_matches_requested() {
        return $this->_matches_requested;
    }

    /**
     * @param string $tournament_games_only
     * @return matches_mapper_web
     */
    public function set_tournament_games_only($tournament_games_only) {
        $tournament_games_only = ($tournament_games_only === true) ? 'true' : 'false';
        $this->_tournament_games_only = $tournament_games_only;
        return $this;
    }

    /**
     * @return string
     */
    public function get_tournament_games_only() {
        return $this->_tournament_games_only;
    }

    /**
     * @return array
     */
    private function _get_data_array() {
        $data = get_object_vars($this);
        $ret = array();
        foreach($data as $key => $value) {
            if (!is_array($value) && !is_null($value)) {
                $ret[ltrim($key, '_')] = $value;
            }
        }
        return $ret;
    }

    /**
     * @return array
     */
    public function load() {
        $request = new request(self::steam_matches_url, $this->_get_data_array());
        $response = $request->send();
        $xml = new SimpleXMLElement($response);

        $matches = array();
        if (isset($xml->matches)) {
		    foreach ($xml->matches as $match) {
                foreach ($match as $m) {
                    $m_id = intval($m->match_id);
                    $matches[$m_id]['match_id'] = $m_id;
                    $matches[$m_id]['start_time'] = intval($m->start_time);
                    $matches[$m_id]['lobby_type'] = intval($m->lobby_type);
                    $matches[$m_id]['match_seq_num'] = intval($m->match_seq_num);
                }
            }
        }
        return $matches;
    }
}
