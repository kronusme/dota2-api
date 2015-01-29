<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\Team;

/**
 * Load info about team from web
 *
 * @author kronus
 * @example
 * <code>
 *   $teamsMapperWeb = new Dota2Api\Mappers\TeamsMapperWeb();
 *   $teams = $teamsMapperWeb->setTeamId(2)->setTeamsRequested(2)->load();
 *   foreach($teams as $team) {
 *     echo $team->get('name');
 *     echo $team->get('rating');
 *     echo $team->get('country_code');
 *     print_r($team->getAllLeaguesIds());
 *   }
 * </code>
 */
class TeamsMapperWeb extends TeamsMapper
{
    /**
     *
     */
    const TEAMS_STEAM_URL = 'https://api.steampowered.com/IDOTA2Match_570/GetTeamInfoByTeamID/v001/';

    /**
     * @return team[]
     */
    public function load()
    {
        $request = new Request(
            self::TEAMS_STEAM_URL,
            array(
                'start_at_team_id' => $this->getTeamId(),
                'teams_requested' => $this->getTeamsRequested()
            )
        );
        $teamsInfo = $request->send();
        if (null === $teamsInfo) {
            return null;
        }
        $teams = array();
        if (isset($teamsInfo->teams)) {
            $teamsInfo = (array)$teamsInfo->teams;
            $teamsInfo = $teamsInfo['team'];
            if (is_array($teamsInfo)) {
                foreach ($teamsInfo as $teamInfo) {
                    $team = $this->getTeam($teamInfo);
                    $teams[$team->get('team_id')] = $team;
                }
                return $teams;
            } else {
                $team = $this->getTeam($teamsInfo);
                $teams[$team->get('team_id')] = $team;
                return $teams;
            }
        }
        return null;
    }

    /**
     * Map one team
     * @param Object $t
     * @return Team team
     */
    protected function getTeam($t)
    {
        $teamInfo = (array)$t;
        $team = new Team();
        $fields = array_keys($teamInfo);
        foreach ($fields as $field) {
            // I hope, that API-response will be changed and players_ids, leagues_ids will become arrays
            if (preg_match('/^player_\d+_account_id$/', $field)) {
                $team->addPlayerId($teamInfo[$field]);
                continue;
            }
            if (preg_match('/^league_id_\d+$/', $field)) {
                $team->addLeagueId($teamInfo[$field]);
                continue;
            }
            $team->set($field, (string)$teamInfo[$field]);
        }
        return $team;
    }
}
