<?php

namespace Dota2Api\Mappers;


/**
 * Common part for team mappers (web and db)
 *
 * @author kronus
 * @package mappers
 */
abstract class TeamsMapper
{
    /**
     * @var int
     */
    protected $_teamId;
    /**
     * @var int
     */
    protected $_teamsRequested;

    /**
     * @return int
     */
    public function getTeamId()
    {
        return $this->_teamId;
    }

    /**
     * @param int $teamId
     * @return TeamsMapper
     */
    public function setTeamId($teamId)
    {
        $teamId = intval($teamId);
        if ($teamId > 0) {
            $this->_teamId = $teamId;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getTeamsRequested()
    {
        return $this->_teamsRequested;
    }

    /**
     * @param int $teamRequested
     * @return TeamsMapper
     */
    public function setTeamsRequested($teamRequested)
    {
        $teamRequested = intval($teamRequested);
        if ($teamRequested >= 1) {
            $this->_teamsRequested = $teamRequested;
        }
        return $this;
    }
}
