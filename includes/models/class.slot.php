<?php
/**
 *
 */
class slot extends stat_object{
    protected $_account_id;
    protected $_player_slot;
    protected $_hero_id;
    protected $_item_0;
    protected $_item_1;
    protected $_item_2;
    protected $_item_3;
    protected $_item_4;
    protected $_item_5;
    protected $_kills;
    protected $_deaths;
    protected $_assists;
    protected $_leaver_status;
    protected $_gold;
    protected $_last_hits;
    protected $_denies;
    protected $_gold_per_min;
    protected $_xp_per_min;
    protected $_gold_spent;
    protected $_hero_damage;
    protected $_tower_damage;
    protected $_hero_healing;
    protected $_level;
    protected $_match_id;
    protected $_abilities_upgrade = array();

    /**
     * @param array $data
     * @return slot
     */
    public function set_abilities_upgrade($data) {
        $this->_abilities_upgrade = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function get_abilities_upgrade() {
        return $this->_abilities_upgrade;
    }
    /**
     *
     */
    public function __construct() {

    }
}
