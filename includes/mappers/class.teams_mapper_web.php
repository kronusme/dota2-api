<?php
/**
 * Load info about team from web
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $teams_mapper_web = new teams_mapper_web();
 *   $teams = $teams_mapper_web->set_team_id(2)->set_teams_requested(2)->load();
 *   foreach($teams as $team) {
 *     echo $team->get('name');
 *     echo $team->get('rating');
 *     echo $team->get('country_code');
 *     print_r($team->get_all_leagues_ids());
 *   }
 * </code>
 */
class teams_mapper_web extends teams_mapper {
    /**
     *
     */
    const teams_steam_url = 'https://api.steampowered.com/IDOTA2Match_570/GetTeamInfoByTeamID/v001/';

    /**
     * @return array
     */
    public function load() {
        $request = new request(
            self::teams_steam_url,
            array(
                'start_at_team_id' => $this->get_team_id(),
                'teams_requested' => $this->get_teams_requested()
            )
        );
        $teams_info = $request->send();
        if (is_null($teams_info)) {
            return null;
        }
        $teams = array();
        if (isset($teams_info->teams)) {
            $teams_info = ((array)$teams_info->teams);
            $teams_info = $teams_info['team'];
            foreach($teams_info as $team_info) {
                $team_info = (array)$team_info;
                $team = new team();
                $fields = array_keys($team_info);
                foreach($fields as $field) {
                    // I hope, that API-response will be changed and players_ids, leagues_ids will become arrays
                    if (preg_match('/^player_\d+_account_id$/', $field)) {
                        $team->add_player_id($team_info[$field]);
                        continue;
                    }
                    if (preg_match('/^league_id_\d+$/', $field)) {
                        $team->add_league_id($team_info[$field]);
                        continue;
                    }
                    $team->set($field, (string)$team_info[$field]);
                }
                $teams[$team->get('team_id')] = $team;
            }
            return $teams;
        }
    }
}
