<?php

namespace Dota2Api\Mappers;

use Dota2Api\Models\LiveSlot;
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
        $leagueId = $this->getLeagueId();
        $requestData = $leagueId ? array('league_id' => $leagueId) : array();
        $request = new Request(
            self::LEAGUE_STEAM_URL,
            $requestData
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
            $picks_bans = array();
            if (array_key_exists('radiant_team', $a_game)) {
                $a_game['radiant_team_id'] = (string)$a_game['radiant_team']->team_id;
                $a_game['radiant_name'] = (string)$a_game['radiant_team']->team_name;
                $a_game['radiant_logo'] = (string)$a_game['radiant_team']->team_logo;
                $a_game['radiant_team_complete'] = ($a_game['radiant_team']->complete === 'false') ? 0 : 1;
            }
            if (array_key_exists('dire_team', $a_game)) {
                $a_game['dire_team_id'] = (string)$a_game['dire_team']->team_id;
                $a_game['dire_name'] = (string)$a_game['dire_team']->team_name;
                $a_game['dire_logo'] = (string)$a_game['dire_team']->team_logo;
                $a_game['dire_team_complete'] = ($a_game['dire_team']->complete === 'false') ? 0 : 1;
            }
            if(array_key_exists('scoreboard', $a_game)) {
                $scoreboard = $a_game['scoreboard'];
                $a_game['duration'] = intval($scoreboard->duration);
                $a_game['roshan_respawn_timer'] = $scoreboard->roshan_respawn_timer;
                if ($scoreboard->radiant) {
                    $radiant = $scoreboard->radiant;
                    $a_game['tower_status_radiant'] = $radiant['tower_state'];
                    $a_game['barracks_status_radiant'] = $radiant['barracks_state'];
                    if ($radiant->players) {
                        foreach($radiant->players->player as $player) {
                            $liveSlot = new LiveSlot();
                            $liveSlot->setArray((array)$player);
                            $liveSlot->set('player_slot', intval($player->player_slot) - 1);
                            $liveMatch->addSlot($liveSlot);
                        }
                    }
                    if ($radiant->picks) {
                        foreach ($radiant->picks->pick as $pick) {
                            array_push($picks_bans,
                                array('is_pick' => true, 'team' => 0, 'order' => 0, 'hero_id' => strval($pick->hero_id)));
                        }
                    }
                    if ($radiant->bans) {
                        foreach ($radiant->bans->ban as $ban) {
                            array_push($picks_bans,
                                array('is_pick' => false, 'team' => 0, 'order' => 0, 'hero_id' => strval($ban->hero_id)));
                        }
                    }
                }
                if ($scoreboard->dire) {
                    $dire = $scoreboard->dire;
                    $a_game['tower_status_dire'] = $dire['tower_state'];
                    $a_game['barracks_status_dire'] = $dire['barracks_state'];
                    if ($dire->players) {
                        foreach($dire->players->player as $player) {
                            $liveSlot = new LiveSlot();
                            $liveSlot->setArray((array)$player);
                            $liveSlot->set('player_slot', intval($player->player_slot) + 127);
                            $liveMatch->addSlot($liveSlot);
                        }
                    }
                    if ($dire->picks) {
                        foreach ($dire->picks->pick as $pick) {
                            array_push($picks_bans,
                                array('is_pick' => true, 'team' => 1, 'order' => 0, 'hero_id' => strval($pick->hero_id)));
                        }
                    }
                    if ($dire->bans) {
                        foreach ($dire->bans->ban as $ban) {
                            array_push($picks_bans,
                                array('is_pick' => false, 'team' => 1, 'order' => 0, 'hero_id' => strval($ban->hero_id)));
                        }
                    }
                }
            }
            $liveMatch->setArray($a_game);
            $liveMatch->setAllPickBans($picks_bans);
            $liveMatches[$liveMatch->get('match_id')] = $liveMatch;
        }
        return $liveMatches;
    }
}
