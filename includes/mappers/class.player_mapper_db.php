<?php
/**
 * All Info About One Player
 */
class player_mapper_db {

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
	 * @return player
	 */
	public function load($id = null) {
		if(!is_null($id)) {
			$this->_steam_id = (string)$id;
		}
		$player = new player();
		
		if(empty($this->_steam_id)) {
			return $player;
		}
		
		$db = db::obtain();
		$result = $db->query_first_pdo('SELECT * FROM ' . db::real_tablename('users') . ' WHERE steamid = ?', array($this->get_steamid()));
		$player->set_array($result);
		return $player;
	}

	/**
	 * Determines whether the player should be inserted or updated in the db
	 * @param player
	 */
	public function save(player $player) {
		if(player_mapper_db::player_exists($player->get('steamid'))) {
			$this->update($player);
		}
		else {
			$this->insert($player);
		}
	}
	
	private function insert(player $player) {
		$db = db::obtain();
		$data = array('account_id' => player::convert_id($player->get('steamid')));
		$data = array_merge($data, $player->get_data_array());
		$db->insert_pdo(db::real_tablename('users'), $data);
	}
	
	private function update(player $player) {
		$db = db::obtain();
		$data = array('account_id' => player::convert_id($player->get('steamid')));
		$data = array_merge($data, $player->get_data_array());
		$db->update_pdo(db::real_tablename('users'), $data, array('steamid' => $player->get('steamid')));
	}
	
	/**
	 * @param string $id
	 * @return bool
	 */
	public static function player_exists($id = null) {
		if(is_null($id)) {
			return false;
		}
		
		$db = db::obtain();
		$result = $db->query_first_pdo('SELECT steamid FROM ' . db::real_tablename('users') . ' WHERE steamid = ?', array($id));
		return $result['steamid'] == (string)$id;
	}
}