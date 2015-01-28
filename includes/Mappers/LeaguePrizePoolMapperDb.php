<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;

/**
 * Load league's prize pool in current moment
 *
 * @example
 * <code>
 *  $prizePoolMapperDb = new Dota2Api\Mappers\LeaguePrizePoolMapperDb();
 *  $pp = $prizePoolMapperDb->setLeagueId(600)->load();
 *  foreach($pp as $date=>$prizePool) {
 *      echo $date.' - $ '.number_format($prizePool, 2).'<br />';
 *  }
 * </code>
 */
class LeaguePrizePoolMapperDb extends LeaguePrizePoolMapper
{

    public function load()
    {
        $leagueid = $this->getLeagueId();
        if (null === $leagueid) {
            return array();
        }
        $db = Db::obtain();
        $checks = array();
        $result = $db->fetchArrayPDO(
            'SELECT * FROM ' . Db::realTablename('league_prize_pools') . ' WHERE league_id = ' . $leagueid,
            array()
        );
        foreach ($result as $r) {
            $checks[$r['date']] = $r['prize_pool'];
        }

        return $checks;
    }

    public function save()
    {
        $leagueid = $this->getLeagueId();
        $prizePool = $this->getPrizePool();
        if (null === $prizePool || null === $leagueid) {
            return;
        }
        $db = Db::obtain();
        $data = array('league_id' => $leagueid, 'prize_pool' => $prizePool);
        $db->insertPDO(Db::realTablename('league_prize_pools'), $data);
        echo $db->getError();
    }
}
