<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\League;

/**
 * Load info about leagues
 *
 * @author kronus
 * @example
 * <code>
 *  $leaguesMapperWeb = new Dota2Api\Mappers\LeaguesMapperWeb();
 *  $leagues = $leaguesMapperWeb->load();
 *  foreach($leagues as $league) {
 *    echo $league->get('description');
 *    if ($league->get('tournament_url')) {
 *      echo $league->get('tournament_url');
 *    }
 *  }
 * </code>
 */
class LeaguesMapperWeb extends LeaguesMapper
{
    /**
     *
     */
    const LEAGUES_STEAM_URL = 'https://api.steampowered.com/IDOTA2Match_570/GetLeagueListing/v0001/';

    /**
     * @return array
     */
    public function load()
    {
        $request = new Request(
            self::LEAGUES_STEAM_URL,
            array()
        );
        $response = $request->send();
        if (null === $response) {
            return null;
        }
        $leaguesInfo = (array)($response->leagues);
        $leaguesInfo = $leaguesInfo['league'];
        $leagues = array();
        foreach ($leaguesInfo as $leagueInfo) {
            $info = (array)$leagueInfo;
            array_walk($info, function (&$v) {
                $v = (string)$v;
            });
            $league = new League();
            $league->setArray($info);
            $leagues[$info['leagueid']] = $league;
        }
        return $leagues;
    }
}
