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
        return null === $leagueLiveMatches ? null : $this->parseLoadedMatches($leagueLiveMatches);
    }

    /**
     * @param $leagueLiveMatches
     * @return LiveMatch[]
     */
    protected function parseLoadedMatches($leagueLiveMatches)
    {
        $leagueLiveMatches = $leagueLiveMatches->games;
        $liveMatches = array();
        if (null === $leagueLiveMatches->game) {
            return array();
        }
        foreach ($leagueLiveMatches->game as $game) {
            $liveMatch = new LiveMatch();
            foreach ($game->players->player as $player) {
                $data = (array)$player;
                $data['match_id'] = $game->match_id;
                switch ($player->team) {
                    case 0:
                        $liveMatch->addRadiantPlayer($data);
                        break;
                    case 1:
                        $liveMatch->addDirePlayer($data);
                        break;
                    case 2:
                        $liveMatch->addBroadcaster($data);
                        break;
                    case 4:
                        $liveMatch->addUnassigned($data);
                        break;
                }
            }
            $a_game = (array)$game;
            $a_game['stage_name'] = strval($a_game['stage_name']);
            $a_game['leagueid'] = $a_game['league_id'];
            unset($a_game['league_id']);
            $picks_bans = array();
            $teams = array('radiant', 'dire');
            foreach ($teams as $team) {
                if (array_key_exists($team . '_team', $a_game)) {
                    $a_game[$team . '_team_id'] = (string)$a_game[$team . '_team']->team_id;
                    $a_game[$team . '_name'] = (string)$a_game[$team . '_team']->team_name;
                    $a_game[$team . '_logo'] = (string)$a_game[$team . '_team']->team_logo;
                    $a_game[$team . '_team_complete'] = ($a_game[$team . '_team']->complete === 'false') ? 0 : 1;
                }
            }
            $liveMatch->setArray($a_game);
            if (array_key_exists('scoreboard', $a_game)) {
                $scoreboard = $a_game['scoreboard'];
                $a_board = (array)$scoreboard;
                $liveMatch->set('duration', intval($a_board['duration']));
                $liveMatch->set('roshan_respawn_timer', intval($a_board['roshan_respawn_timer']));

                if ($scoreboard->radiant) {
                    $this->parseScoreboard($liveMatch, $scoreboard, 'radiant');
                }
                if ($scoreboard->dire) {
                    $this->parseScoreboard($liveMatch, $scoreboard, 'dire');
                }
            }
            $liveMatch->setAllPickBans($picks_bans);
            $liveMatches[$liveMatch->get('match_id')] = $liveMatch;
        }
        return $liveMatches;
    }

    /**
     * @param LiveMatch $liveMatch
     * @param Object $scoreboard
     * @param string $teamSide
     * @return LiveMatch
     */
    private function parseScoreboard(&$liveMatch, $scoreboard, $teamSide)
    {
        $team = $scoreboard->{$teamSide};
        $liveMatch->set($teamSide.'_score', strval($team->score));
        $liveMatch->set('tower_status_' . $teamSide, strval($team->tower_state));
        $liveMatch->set('barracks_status_' . $teamSide, strval($team->barracks_state));
        if ($team->players) {
            foreach ($team->players->player as $player) {
                $liveSlot = new LiveSlot();
                $slotData = (array)$player;
                for ($i = 0; $i <= 5; $i++) {
                    if (!array_key_exists('item_'.$i, $slotData) && array_key_exists('item'.$i, $slotData)) {
                        $slotData['item_'.$i] = $slotData['item'.$i];
                        unset($slotData['item'.$i]);
                    }
                }
                $liveSlot->setArray($slotData);
                $liveSlot->set('match_id', $liveMatch->get('match_id'));
                $liveSlot->set('player_slot', $this->getPlayerSlot($player->player_slot, $teamSide));
                $liveMatch->addSlot($liveSlot);
            }
        }
        $fl = $team === 'radiant' ? 0 : 1;
        if ($team->picks) {
            foreach ($team->picks->pick as $pick) {
                $liveMatch->addPickBan($this->getPickBanItem(true, $fl, 0, $pick->hero_id));
            }
        }
        if ($team->bans) {
            foreach ($team->bans->ban as $ban) {
                $liveMatch->addPickBan($this->getPickBanItem(false, $fl, 0, $ban->hero_id));
            }
        }
        return $liveMatch;
    }

    private function getPlayerSlot($val, $teamSide)
    {
        $val = intval($val);
        return $teamSide === 'radiant' ? $val - 1 : $val + 127;
    }

    /**
     * @param boolean $isPick
     * @param 0|1 $team
     * @param integer $order
     * @param integer|string $heroId
     * @return array
     */
    private function getPickBanItem($isPick, $team, $order, $heroId)
    {
        return array(
            'is_pick' => $isPick,
            'team' => $team,
            'order' => $order,
            'hero_id' => strval($heroId)
        );
    }
}
