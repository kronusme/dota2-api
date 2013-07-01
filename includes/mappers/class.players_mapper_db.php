<?php
/**
 * Load info about players from database
 */
class players_mapper_db {
	/**
	 * @var array
	 */
	private $_ids = array();
	
	/**
	 * @param $id
	 * @return players_mapper_db
	 */
	 public function add_id($id) {
		 $id = (string)$id;
		 if(!in_array($id, $this->_ids)) {
			 array_push($this->_ids, $id);
		 }
		 
		 return $this;
	 }
	 
	 public function remove_id($id) {
		$id = (string)$id;
		foreach($this->_ids as $k=>$v) {
			if($v == $id) {
				unset($this->_ids[$k]);
			}
		}
		return $this;
	 }
	 
	 /**
	  * Removes all ids
	  * @return players_mapper_db
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
	    * Default Constructor
	    */
	   public function __construct() {
	   }
	   
	   public function load() {
		   $db = db::obtain();
		   $players = array();
		   $result = $db->fetch_array_pdo('SELECT * FROM users WHERE steamid IN (' . $this->get_ids_string() . ')', array());
		   foreach($result as $r) {
			   $player = new player();
			   $player->set_array((array)$r);
			   $players[$player->get('steamid')] = $player;
		   }
		   
		   return $players;
	   }
}
?>
