<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\LiveMatch;

/**
 * Load live league matches
 *
 * @author kronus
 * @example
 * <code>
 *  $leagueMapper = new Dota2Api\Mappers\LeagueMapper(22); // set league id (can be get via leaguesMapper)
 *  $games = $leagueMapper->load();
 *  print_r($games);
 * </code>
 */
class LeagueMapper
{
    /**
     *
     */
    const LEAGUE_STEAM_URL = 'https://api.steampowered.com/IDOTA2Match_570/GetLiveLeagueGames/v0001/';
    /**
     * @var int
     */
    protected $_leagueId;

    /**
     * @param int $leagueId
     * @return LeagueMapper
     */
    public function setLeagueId($leagueId)
    {
        $this->_leagueId = (int)$leagueId;
        return $this;
    }

    /**
     * @return int
     */
    public function getLeagueId()
    {
        return $this->_leagueId;
    }

    /**
     * @param int $leagueId
     */
    public function __construct($leagueId = null)
    {
        if (null !== $leagueId) {
            $this->setLeagueId($leagueId);
        }
    }

    /**
     *
     */
    public function load()
    {
        $request = new Request(
            self::LEAGUE_STEAM_URL,
            array('league_id' => $this->getLeagueId())
        );
        $leagueLiveMatches = $request->send();
        if (null === $leagueLiveMatches) {
            return null;
        }
        $leagueLiveMatches = $leagueLiveMatches->games;
        $liveMatches = array();
        if (null === $leagueLiveMatches->game) {
            return array();
        }
        foreach ($leagueLiveMatches->game as $game) {
            $liveMatch = new LiveMatch();
            foreach ($game->players->player as $player) {
                switch ($player->team) {
                    case 0:
                        $liveMatch->addRadiantPlayer((array)$player);
                        break;
                    case 1:
                        $liveMatch->addDirePlayer((array)$player);
                        break;
                    case 2:
                        $liveMatch->addBroadcaster((array)$player);
                        break;
                    case 4:
                        $liveMatch->addUnassigned((array)$player);
                        break;
                }
            }
            $a_game = (array)$game;
            unset($a_game['players']);
            $a_game['radiant_team_id'] = (string)$a_game['radiant_team']->team_id;
            $a_game['radiant_name'] = (string)$a_game['radiant_team']->team_name;
            $a_game['radiant_logo'] = (string)$a_game['radiant_team']->team_logo;
            $a_game['radiant_team_complete'] = ($a_game['radiant_team']->complete === 'false') ? 0 : 1;
            $a_game['dire_team_id'] = (string)$a_game['dire_team']->team_id;
            $a_game['dire_name'] = (string)$a_game['dire_team']->team_name;
            $a_game['dire_logo'] = (string)$a_game['dire_team']->team_logo;
            $a_game['dire_team_complete'] = ($a_game['dire_team']->complete === 'false') ? 0 : 1;
            unset($a_game['dire_team'], $a_game['radiant_team']);
            $liveMatch->setArray($a_game);
            array_push($liveMatches, $liveMatch);
        }
        return $liveMatches;
    }
}
