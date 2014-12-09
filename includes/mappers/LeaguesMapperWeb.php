<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\League;

/**
 * Load info about leagues
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *  $leagues_mapper_web = new leagues_mapper_web();
 *  $leagues = $leagues_mapper_web->load();
 *  foreach($leagues as $league) {
 *    echo $league->get('description');
 *    if ($league->get('tournament_url')) {
 *      echo $league->get('tournament_url');
 *    }
 *  }
 * </code>
 */
class LeaguesMapperWeb extends LeaguesMapper {
    /**
     *
     */
    const leagues_steam_url = 'https://api.steampowered.com/IDOTA2Match_570/GetLeagueListing/v0001/';

    /**
     *
     */
    public function __construct(){}

    /**
     * @return array
     */
    public function load() {
        $request = new Request(
            self::leagues_steam_url,
            array()
        );
        $response = $request->send();
        if (is_null($response)) {
            return null;
        }
        $leagues_info = (array)($response->leagues);
        $leagues_info = $leagues_info['league'];
        $leagues = array();
        foreach($leagues_info as $league_info) {
            $info = (array)$league_info;
            array_walk($info, function (&$v) {
                $v = (string)$v;
            });
            $league = new League();
            $league->set_array($info);
            $leagues[$info['leagueid']] = $league;
        }
        return $leagues;
    }
}
