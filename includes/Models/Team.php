<?php

namespace Dota2Api\Models;

/**
 * All info about one team
 *
 * @author kronus
 */
class Team extends StatObject
{
    /**
     * the numeric ID of the team
     * @var int
     */
    protected $_team_id;
    /**
     * the name of the team
     * @var string
     */
    protected $_name;
    /**
     * the team's abbreviation tag
     * @var string
     */
    protected $_tag;
    /**
     * the Unix time at which the team was created
     * @var int
     */
    protected $_time_created;
    /**
     * team mm-rating
     * @var int
     */
    protected $_rating;
    /**
     * the team's logo (see - http://dev.dota2.com/showthread.php?t=71363&p=462059&viewfull=1#post462059)
     * @var string
     */
    protected $_logo;
    /**
     * the image showing the team's sponsor(s) (see - http://dev.dota2.com/showthread.php?t=71363&p=462059&viewfull=1#post462059)
     * @var string
     */
    protected $_logo_sponsor;
    /**
     * the country the team is from (see http://en.wikipedia.org/wiki/ISO_3166-1#Current_codes) (empty string if not specified)
     * @var string
     */
    protected $_country_code;
    /**
     * the number of team matchmaking games the team has played
     * @var int
     */
    protected $_games_played_with_current_roster;
    /**
     * the account id of the player who is the administrator of the team in the dota client
     * @var int
     */
    protected $_admin_account_id;
    /**
     * the team's homepage (empty string if not specified)
     * @var string
     */
    protected $_url;
    /**
     * array of team-players ids
     * @var array
     */
    protected $_players_ids = array();
    /**
     * array of leagues ids where this team plays
     * @var array
     */
    protected $_leagues_ids = array();

    /**
     * @return array
     */
    public function getAllPlayersIds()
    {
        return $this->_players_ids;
    }

    /**
     * @param array $ids
     * @return team
     */
    public function setAllPlayersIds(array $ids)
    {
        foreach ($ids as $id) {
            $this->addPlayerId($id);
        }
        return $this;
    }

    /**
     * @param int $index
     * @return int|null
     */
    public function getPlayerId($index)
    {
        $index = (int)$index;
        return array_key_exists($index, $this->_players_ids) ? $this->_players_ids[$index] : null;
    }

    /**
     * @param int $player_id
     * @return team
     */
    public function addPlayerId($player_id)
    {
        $player_id = (int)$player_id;
        array_push($this->_players_ids, $player_id);
        return $this;
    }

    /**
     * @return array
     */
    public function getAllLeaguesIds()
    {
        return $this->_leagues_ids;
    }

    /**
     * @param array $leagues
     * @return team
     */
    public function setAllLeaguesIds(array $leagues)
    {
        foreach ($leagues as $id) {
            $this->addLeagueId($id);
        }
        return $this;
    }

    /**
     * @param int $index
     * @return int|null
     */
    public function getLeaguesId($index)
    {
        $index = (int)$index;
        return array_key_exists($index, $this->_leagues_ids) ? $this->_leagues_ids[$index] : null;
    }

    /**
     * @param int $league_id
     * @return team
     */
    public function addLeagueId($league_id)
    {
        $league_id = (int)$league_id;
        array_push($this->_leagues_ids, $league_id);
        return $this;
    }
}
