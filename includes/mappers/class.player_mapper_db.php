<?php
/**
 * All Info About One Player
 */
class player_mapper_db extends player_db {
	public function __construct() {
	}
	
	public function set_steamid($id) {
		$this->_steam_id = (string)$id;
	}
	
	public function get_steamid() {
		return $this->_steam_id;
	}
	
	/**
	 * @param steam_id or null
	 * @return player_db object
	 */
	public function load($id = null) {
		if(!is_null($id)) {
			$this->_steam_id = (string)$id;
		}
		$player = new player_db();
		
		if(empty($this->_steam_id)) {
			return $player;
		}
		
		$db = db::obtain();
		$result = $db->query_first_pdo('SELECT * FROM ' . db::real_tablename('users') . ' WHERE steam_id = ?', array($this->get_steamid()));
		$player->set_array($result);
		return $player;
	}
	
	public function save(player_db $player) {
		if(player_mapper_db::player_exists($player->get('steam_id'))) {
			$this->update($player);
		}
		else {
			$this->insert($player);
		}
	}
	
	private function insert($player) {
		$db = db::obtain();
		$data = array(
			'personaname' => $player->get('personaname'),
			'steam_id' => $player->get('steam_id'),
			'community_visibility_state' => $player->get('communityvisibilitystate'),
			'profile_state' => $player->get('profilestate'),
			'last_logoff' => $player->get('lastlogoff'),
			'comment_permission' => $player->get('commentpermission'),
			'profile_url' => $player->get('profileurl'),
			'avatar' => $player->get('avatar'),
			'avatar_medium' => $player->get('avatarmedium'),
			'avatar_full' => $player->get('avatarfull'),
			'personastate' => $player->get('personastate'),
			'real_name' => $player->get('realname'),
			'primary_clan_id' => $player->get('primaryclanid'),
			'time_created' => $player->get('timecreated')
			);
		$db->insert_pdo(db::real_tablename('users'), $data);
	}
	
	private function update($player) {
		$db = db::obtain();
		$data = array(
			'personaname' => $player->get('personaname'),
			'steam_id' => $player->get('steam_id'),
			'community_visibility_state' => $player->get('communityvisibilitystate'),
			'profile_state' => $player->get('profilestate'),
			'last_logoff' => $player->get('lastlogoff'),
			'comment_permission' => $player->get('commentpermission'),
			'profile_url' => $player->get('profileurl'),
			'avatar' => $player->get('avatar'),
			'avatar_medium' => $player->get('avatarmedium'),
			'avatar_full' => $player->get('avatarfull'),
			'personastate' => $player->get('personastate'),
			'real_name' => $player->get('realname'),
			'primary_clan_id' => $player->get('primaryclanid'),
			'time_created' => $player->get('timecreated')
			);
		$db->update_pdo(db::real_tablename('users'), $data, array('steam_id' => $player->get('steam_id')));
	}
	
	/**
	 * @param string steam_id
	 * @return bool
	 */
	public static function player_exists($id = null) {
		if(is_null($id)) {
			return;
		}
		
		$db = db::obtain();
		$result = $db->query_first_pdo('SELECT * FROM ' . db::real_tablename('users') . ' WHERE steam_id = ?', array($id));
		if($result['steam_id'] == $this->get_steamid) {
			return true;
		}
		
		return false;
	}
}
?>
