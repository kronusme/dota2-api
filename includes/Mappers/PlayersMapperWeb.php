<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\Player;

/**
 * Load info about players
 *
 * @example
 * <code>
 *  $playersMapperWeb = new Dota2Api\Mappers\PlayersMapperWeb();
 *  $playersInfo = $playersMapperWeb->addId('76561198067833250')->addId('76561198058587506')->load();
 *  foreach($playersInfo as $playerInfo) {
 *    echo $playerInfo->get('realname');
 *    echo '<img src="'.$playerInfo->get('avatarfull').'" alt="'.$playerInfo->get('personaname').'" />';
 *    echo '<a href="'.$playerInfo->get('profileurl').'">'.$playerInfo->get('personaname').'\'s steam profile</a>';
 *  }
 *  print_r($playersInfo);
 * </code>
 */
class PlayersMapperWeb
{
    /**
     *
     */
    const PLAYER_STEAM_URL = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/';
    /**
     * @var array
     */
    private $_ids = array();

    /**
     * @param $id
     * @return PlayersMapperWeb
     */
    public function addId($id)
    {
        $id = (string)$id;
        if (!in_array($id, $this->_ids, true)) {
            array_push($this->_ids, $id);
        }
        return $this;
    }

    /**
     * @param $id
     * @return PlayersMapperWeb
     */
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
     * @return PlayersMapperWeb
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
     *
     */
    public function __construct()
    {

    }

    /**
     * @return Player[]
     */
    public function load()
    {
        $request = new Request(self::PLAYER_STEAM_URL, array('steamids' => $this->getIdsString()));
        $playersInfo = $request->send();
        if (null === $playersInfo) {
            return null;
        }
        $players = array();
        foreach ($playersInfo->players[0] as $playerInfo) {
            $player = new Player();
            $player->setArray((array)$playerInfo);
            $players[$player->get('steamid')] = $player;
        }
        return $players;
    }
}
