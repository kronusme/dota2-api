<?php

namespace Dota2Api\Mappers;

/**
 * Common part for league's prize pool mappers (web and db)
 *
 * @author kronus
 */
abstract class LeaguePrizePoolMapper
{

    /**
     * @var int
     */
    protected $_leagueId;

    /**
     * @var int
     */
    protected $_prizePool;

    /**
     * @param int $leagueId
     * @return LeaguePrizePoolMapper
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
     * @param int $prizePool
     * @return LeaguePrizePoolMapper
     */
    public function setPrizePool($prizePool)
    {
        $this->_prizePool = (int)$prizePool;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrizePool()
    {
        return $this->_prizePool;
    }
}
