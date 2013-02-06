<?php
/**
 *
 */
class players_mapper {
    /**
     *
     */
    const player_steam_url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/';
    /**
     * @var array
     */
    private $_ids = array();

    /**
     * @param $id
     * @return players_mapper
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
     * @return players_mapper
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
     * @return players_mapper
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
     *
     */
    public function __construct() {

    }

    /**
     * @return array
     */
    public function get_info() {
        $request = new request(self::player_steam_url, array('steamids' => $this->get_ids_string()));
        $response = $request->send();
        $players_info = new SimpleXMLElement($response);
        $players = array();
        foreach($players_info->players[0] as $player) {
            $players[(string)$player->steamid] = (array)$player;
        }
        return $players;
    }
}