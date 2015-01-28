<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\Player;

/**
 * Load info about players from database
 */
class PlayersMapperDb
{
    /**
     * @var array
     */
    private $_ids = array();

    /**
     * @param $id
     * @return PlayersMapperDb
     */
    public function addId($id)
    {
        $id = (string)$id;
        if (!in_array($id, $this->_ids, true)) {
            array_push($this->_ids, $id);
        }

        return $this;
    }

    public function removeId($id)
    {
        $id = (string)$id;
        foreach ($this->_ids as $k => $v) {
            if ($v === $id) {
                unset($this->_ids[$k]);
            }
        }
        return $this;
    }

    /**
     * Removes all ids
     * @return PlayersMapperDb
     */
    public function removeIds()
    {
        $this->_ids = array();
        return $this;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->_ids;
    }

    /**
     * @return string
     */
    public function getIdsString()
    {
        return implode(',', $this->getIds());
    }

    /**
     * Default Constructor
     */
    public function __construct()
    {
    }

    public function load()
    {
        $db = Db::obtain();
        $players = array();
        $ids = $this->getIdsString();
        if (count($this->_ids) === 0) {
            return array();
        }
        $result = $db->fetchArrayPDO(
            'SELECT * FROM ' . Db::realTablename('users') . ' WHERE steamid IN (' . $ids . ')',
            array()
        );
        foreach ($result as $r) {
            $player = new Player();
            $player->setArray((array)$r);
            $players[$player->get('steamid')] = $player;
        }

        return $players;
    }
}
