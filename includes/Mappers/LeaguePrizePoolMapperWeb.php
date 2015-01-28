<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;

/**
 * Load league's prize pool in current moment
 *
 * @example
 * <code>
 *  $leaguePrizePoolMapperWeb = new Dota2Api\Mappers\LeaguePrizePoolMapperWeb();
 *  $leaguePrizePoolMapperWeb->setLeagueId(600);
 *  $prizePoolInfo = $leaguePrizePoolMapperWeb->load();
 *  print_r($prizePoolInfo);
 *  echo $prizePoolInfo['prize_pool'];
 *  echo $prizePoolInfo['league_id'];
 *  echo $prizePoolInfo['status']; // may be undefined
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
        if (null !== $leagueId) {
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
        if (null === $prizePool) {
            return null;
        }
        return (array)$prizePool;
    }
}
