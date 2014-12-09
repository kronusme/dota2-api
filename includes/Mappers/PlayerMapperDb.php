<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\Player;

/**
 * All Info About One Player
 */
class PlayerMapperDb {

    protected $_steam_id;

	public function __construct() {
	}
	
	public function set_steamid($id) {
		$this->_steam_id = (string)$id;
	}
	
	public function get_steamid() {
		return $this->_steam_id;
	}
	
	/**
	 * @param number $id or null
	 * @return Player
	 */
	public function load($id = null) {
		if(!is_null($id)) {
			$this->_steam_id = (string)$id;
		}
		$player = new Player();
		
		if(empty($this->_steam_id)) {
			return $player;
		}
		
		$db = Db::obtain();
		$result = $db->query_first_pdo('SELECT * FROM ' . Db::real_tablename('users') . ' WHERE steamid = ?', array($this->get_steamid()));
		$player->set_array($result);
		return $player;
	}

	/**
	 * Determines whether the player should be inserted or updated in the db
	 * @param Player
	 */
	public function save(player $player) {
		if(self::player_exists($player->get('steamid'))) {
			$this->update($player);
		}
		else {
			$this->insert($player);
		}
	}
	
	private function insert(Player $player) {
		$db = Db::obtain();
		$data = array('account_id' => Player::convert_id($player->get('steamid')));
		$data = array_merge($data, $player->get_data_array());
		$db->insert_pdo(Db::real_tablename('users'), $data);
	}
	
	private function update(Player $player) {
		$db = Db::obtain();
		$data = array('account_id' => Player::convert_id($player->get('steamid')));
		$data = array_merge($data, $player->get_data_array());
		$db->update_pdo(Db::real_tablename('users'), $data, array('steamid' => $player->get('steamid')));
	}
	
	/**
	 * @param string $id
	 * @return bool
	 */
	public static function player_exists($id = null) {
		if(is_null($id)) {
			return false;
		}
		
		$db = Db::obtain();
		$result = $db->query_first_pdo('SELECT steamid FROM ' . Db::real_tablename('users') . ' WHERE steamid = ?', array($id));
		return $result['steamid'] == (string)$id;
	}
}