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
     * @var int
     */
    protected $_league_tier;
    /**
     * @var int
     */
    protected $_series_id;
    /**
     * @var int
     */
    protected $_game_number;
    /**
     * @var int
     */
    protected $_stream_delay_s;
    /**
     * @var int
     */
    protected $_radiant_series_wins;
    /**
     * @var int
     */
    protected $_dire_series_wins;
    /**
     * @var int
     */
    protected $_series_type;
    /**
     * @var int
     */
    protected $_league_series_id;
    /**
     * @var int
     */
    protected $_league_game_id;
    /**
     * @var string
     */
    protected $_stage_name;
    /**
     * @var int
     */
    protected $_roshan_respawn_timer;
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

    public function getDataArray()
    {
        $data = get_object_vars($this);
        $ret = array();
        foreach ($data as $key => $value) {
            if (!is_array($value) && !is_null($value)) {
                $ret[ltrim($key, '_')] = $value;
            }
        }
        return $ret;
    }
}
