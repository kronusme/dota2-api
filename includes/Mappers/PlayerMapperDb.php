<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\Player;

/**
 * All Info About One Player
 */
class PlayerMapperDb
{

    protected $_steam_id;

    public function __construct()
    {
    }

    public function setSteamid($id)
    {
        $this->_steam_id = (string)$id;
    }

    public function getSteamid()
    {
        return $this->_steam_id;
    }

    /**
     * @param number|string $id or null
     * @return Player
     */
    public function load($id = null)
    {
        if (null !== $id) {
            $this->_steam_id = (string)$id;
        }
        $player = new Player();

        if (empty($this->_steam_id)) {
            return $player;
        }

        $db = Db::obtain();
        $result = $db->queryFirstPDO(
            'SELECT * FROM ' . Db::realTablename('users') . ' WHERE steamid = ?',
            array($this->getSteamid())
        );
        $player->setArray($result);
        return $player;
    }

    /**
     * Determines whether the player should be inserted or updated in the db
     * @param Player $player
     */
    public function save(Player $player)
    {
        if (self::playerExists($player->get('steamid'))) {
            $this->update($player);
        } else {
            $this->insert($player);
        }
    }

    private function insert(Player $player)
    {
        $db = Db::obtain();
        $data = array('account_id' => Player::convertId($player->get('steamid')));
        $data = array_merge($data, $player->getDataArray());
        $db->insertPDO(Db::realTablename('users'), $data);
    }

    private function update(Player $player)
    {
        $db = Db::obtain();
        $data = array('account_id' => Player::convertId($player->get('steamid')));
        $data = array_merge($data, $player->getDataArray());
        $db->updatePDO(Db::realTablename('users'), $data, array('steamid' => $player->get('steamid')));
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function playerExists($id = null)
    {
        if (null === $id) {
            return false;
        }

        $db = Db::obtain();
        $result = $db->queryFirstPDO(
            'SELECT steamid FROM ' . Db::realTablename('users') . ' WHERE steamid = ?',
            array($id)
        );
        return $result['steamid'] === (string)$id;
    }
}
