<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\Player;

/**
 * Load info about players from database
 */
class PlayersMapperDb {
	/**
	 * @var array
	 */
	private $_ids = array();
	
	/**
	 * @param $id
	 * @return PlayersMapperDb
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
	  * @return PlayersMapperDb
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
		   $db = Db::obtain();
		   $players = array();
           $ids = $this->get_ids_string();
           if (count($this->_ids) === 0) {
               return array();
           }
		   $result = $db->fetch_array_pdo('SELECT * FROM '.Db::real_tablename('users').' WHERE steamid IN (' . $ids . ')', array());
		   foreach($result as $r) {
			   $player = new Player();
			   $player->set_array((array)$r);
			   $players[$player->get('steamid')] = $player;
		   }
		   
		   return $players;
	   }
}