<?php
/**
 * Load live league matches
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *  $league_mapper = new league_mapper(22); // set league id (can be get via leagues_mapper)
 *  $games = $league_mapper->load();
 *  print_r($games);
 * </code>
 */
class league_mapper {
    /**
     *
     */
    const league_steam_url = 'https://api.steampowered.com/IDOTA2Match_570/GetLiveLeagueGames/v0001/';
    /**
     * @var int
     */
    protected $_league_id;

    /**
     * @param int $league_id
     * @return league_mapper
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
     * @param int $league_id
     */
    public function __construct($league_id = null) {
        if (!is_null($league_id)) {
            $this->set_league_id($league_id);
        }
    }

    /**
     *
     */
    public function load() {
        $request = new request(
            self::league_steam_url,
            array('league_id' => $this->get_league_id())
        );
        $league_live_matches = $request->send();
        if (is_null($league_live_matches)) {
            return null;
        }
        $league_live_matches = $league_live_matches->games;
        $live_matches = array();
        if (!isset($league_live_matches->game)) {
            return array();
        }
        foreach($league_live_matches->game as $game) {
            $live_match = new live_match();
            foreach($game->players->player as $player) {
                switch ($player->team) {
                    case 0: {
                        $live_match->add_radiant_player((array)$player);
                        break;
                    }
                    case 1: {
                        $live_match->add_dire_player((array)$player);
                        break;
                    }
                    case 2: {
                        $live_match->add_broadcaster((array)$player);
                        break;
                    }
                    case 4: {
                        $live_match->add_unassigned((array)$player);
                        break;
                    }
                }
            }
            $a_game = (array)$game;
            unset($a_game['players']);
            $a_game['radiant_team_id'] = (string)$a_game['radiant_team']->team_id;
            $a_game['radiant_name'] = (string)$a_game['radiant_team']->team_name;
            $a_game['radiant_logo'] = (string)$a_game['radiant_team']->team_logo;
            $a_game['radiant_team_complete'] = ($a_game['radiant_team']->complete == 'false')? 0 : 1;
            $a_game['dire_team_id'] = (string)$a_game['dire_team']->team_id;
            $a_game['dire_name'] = (string)$a_game['dire_team']->team_name;
            $a_game['dire_logo'] = (string)$a_game['dire_team']->team_logo;
            $a_game['dire_team_complete'] = ($a_game['dire_team']->complete == 'false')? 0 : 1;
            unset($a_game['dire_team']);
            unset($a_game['radiant_team']);
            $live_match->set_array($a_game);
            array_push($live_matches, $live_match);
        }
        return $live_matches;
    }
}