<?php
/**
 * Load info about players
 *
 * @example
 * <code>
 *  $players_mapper_web = new players_mapper_web();
 *  $players_info = $players_mapper_web->add_id('76561198067833250')->add_nickname('Nimf0manka')->load();
 *  foreach($players_info as $player_info) {
 *    echo $player_info->get('realname');
 *    echo '<img src="'.$player_info->get('avatarfull').'" alt="'.$player_info->get('personaname').'" />';
 *    echo '<a href="'.$player_info->get('profileurl').'">'.$player_info->get('personaname').'\'s steam profile</a>';
 *  }
 *  print_r($players_info);
 * </code>
 */
class players_mapper_web {
    /**
     *
     */
    const player_steam_url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/';
    /**
     * 
     */
    const resolve_steam_url = 'http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/';
    /**
     * @var array
     */
    private $_ids = array();
    /**
     * @var array
     */
    private $_nicknames = array();

    /**
     * @param $id
     * @return players_mapper_web
     */
    public function add_id($id) {
        $id = (string)$id;
        if (!in_array($id, $this->_ids)) {
            array_push($this->_ids, $id);
        }
        return $this;
    }

    /**
     * @param $id
     * @return players_mapper_web
     */
    public function remove_id($id) {
        $id = (string)$id;
        foreach($this->_ids as $k => $v) {
            if ($v == $id) {
                unset($this->_ids[$k]);
            }
        }
        return $this;
    }

    /**
     * @return players_mapper_web
     */
    public function remove_ids() {
        $this->_ids = array();
        return $this;
    }

    /**
     * @return array
     */
    public function get_ids() {
        return $this->_ids;
    }

    /**
     * @return string
     */
    public function get_ids_string() {
        return implode(',', $this->get_ids());
    }

    /**
     * @param $nickname
     * @return players_mapper_web
     */
    public function add_nickname($nickname)
    {
        $nickname = (string)$nickname;
        if (!in_array($nickname, $this->_nicknames)) {
            array_push($this->_nicknames, $nickname);
        }
        return $this;
    }

    public function remove_nickname($nickname)
    {
        $nickname = (string)$nickname;
        foreach($this->_nicknames as $k => $v) {
            if ($v == $nickname) {
                unset($this->_ids[$k]);
            }
        }
        return $this;
    }

    /**
    * @return players_mapper_web
    */
    public function remove_nicknames() {
        $this->_nicknames = array();
        return $this;
    }

    /**
     * @return array
     */
    public function get_nicknames() {
        return $this->_nicknames;
    }

    /**
     * @return string
     */
    public function get_nicknames_string() {
        return implode(',', $this->get_nicknames());
    }

    /**
     *
     */
    public function __construct() {

    }

    /**
     * @return array
     */
    public function load() {
        foreach ($this->_nicknames as $k => $v) {
            $request = new request(self::resolve_steam_url, array('vanityurl' => $v));
            $nickname_info = $request->send();
            if (is_null($nickname_info)) {
                continue;
            }
            $id = $nickname_info->steamid;
            if (!in_array($id, $this->_ids)) {
                array_push($this->_ids, $id);
            }
        }

        $request = new request(self::player_steam_url, array('steamids' => $this->get_ids_string()));

        $players_info = $request->send();
        if (is_null($players_info)) {
            return null;
        }
        $players = array();
        foreach($players_info->players[0] as $player_info) {
            $player = new player();
            $player->set_array((array)$player_info);
            $players[$player->get('steamid')] = $player;
        }
        return $players;
    }
}