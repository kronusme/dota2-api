<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;

/**
 * Load league's prize pool in current moment
 *
 * @example
 * <code>
 *  $league_prize_pool_mapper_web = new league_prize_pool_mapper_web();
 *  $league_prize_pool_mapper_web->set_league_id(600);
 *  $prize_pool_info = $league_prize_pool_mapper_web->load();
 *  print_r($prize_pool_info);
 *  echo $prize_pool_info['prize_pool'];
 *  echo $prize_pool_info['league_id'];
 *  echo $prize_pool_info['status']; // may be undefined
 * </code>
 */
class LeaguePrizePoolMapperWeb extends LeaguePrizePoolMapper {

    /**
     * Request url
     */
    const leagues_prize_pool_steam_url = 'http://api.steampowered.com/IEconDOTA2_570/GetTournamentPrizePool/v1/';

    /**
     * @param int $league_id
     */
    public function __construct($league_id = null) {
        if (!is_null($league_id)) {
            $this->set_league_id($league_id);
        }
    }

    public function load() {
        $request = new Request(
            self::leagues_prize_pool_steam_url,
            array('leagueid' => $this->get_league_id())
        );
        $prize_pool = $request->send();
        if (is_null($prize_pool)) {
            return null;
        }
        return (array)$prize_pool;
    }
}