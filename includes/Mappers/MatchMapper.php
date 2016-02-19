<?php

namespace Dota2Api\Mappers;

/**
 * Common part for match mappers (web and db)
 *
 * @author kronus
 */
abstract class MatchMapper
{
    /**
     * @var int
     */
    private $_match_id;

    /**
     * @param int $matchId
     * @return MatchMapper
     */
    public function setMatchId($matchId = null)
    {
        if (null !== $matchId) {
            $this->_match_id = $matchId;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getMatchId()
    {
        return $this->_match_id;
    }

    /**
     * @param int $matchId
     */
    public function __construct($matchId = null)
    {
        if (null !== $matchId) {
            $this->setMatchId($matchId);
        }
    }

    /**
     * Load info by match_id
     * @return mixed
     */
    abstract public function load();
}
