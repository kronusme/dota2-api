<?php

namespace Dota2Api\Models;

/**
 * Live match. Data about match that is playing now. Not all common match-info is available
 *
 * @author kronus
 */
class LiveMatch extends Match
{
    /**
     * @var int
     */
    protected $_lobby_id;
    /**
     * @var int
     */
    protected $_spectators;
    /**
     * @var int
     */
    protected $_tower_state;
    /**
     * @var int
     */
    protected $_league_id;
    /**
     * @var array
     */
    protected $_radiant_team = array();
    /**
     * @var array
     */
    protected $_dire_team = array();
    /**
     * @var array
     */
    protected $_broadcasters = array();

    /**
     * @var array
     */
    protected $_unassigned = array();

    /**
     * @param array $broadcaster
     * @return LiveMatch
     */
    public function addBroadcaster(array $broadcaster)
    {
        $this->_broadcasters[$broadcaster['account_id']] = $broadcaster;
        return $this;
    }

    /**
     * @param array $unassigned
     * @return LiveMatch
     */
    public function addUnassigned(array $unassigned)
    {
        $this->_unassigned[$unassigned['account_id']] = $unassigned;
        return $this;
    }

    /**
     * @param array $player_info
     * @return LiveMatch
     */
    public function addRadiantPlayer(array $player_info)
    {
        $this->_radiant_team[$player_info['account_id']] = $player_info;
        return $this;
    }

    /**
     * @param array $player_info
     * @return LiveMatch
     */
    public function addDirePlayer(array $player_info)
    {
        $this->_dire_team[$player_info['account_id']] = $player_info;
        return $this;
    }
}
