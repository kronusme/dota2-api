<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;

/**
 * Load league's prize pool in current moment
 *
 * @example
 * <code>
 *  $league_prize_pool_mapper_web = new league_prize_pool_mapper_web();
 *  $league_prize_pool_mapper_web->setLeagueId(600);
 *  $prize_pool_info = $league_prize_pool_mapper_web->load();
 *  print_r($prize_pool_info);
 *  echo $prize_pool_info['prize_pool'];
 *  echo $prize_pool_info['league_id'];
 *  echo $prize_pool_info['status']; // may be undefined
 * </code>
 */
class LeaguePrizePoolMapperWeb extends LeaguePrizePoolMapper
{

    /**
     * Request url
     */
    const LEAGUES_PRIZE_POOL_STEAM_URL = 'http://api.steampowered.com/IEconDOTA2_570/GetTournamentPrizePool/v1/';

    /**
     * @param int $leagueId
     */
    public function __construct($leagueId = null)
    {
        if (!is_null($leagueId)) {
            $this->setLeagueId($leagueId);
        }
    }

    public function load()
    {
        $request = new Request(
            self::LEAGUES_PRIZE_POOL_STEAM_URL,
            array('leagueid' => $this->getLeagueId())
        );
        $prizePool = $request->send();
        if (is_null($prizePool)) {
            return null;
        }
        return (array)$prizePool;
    }
}