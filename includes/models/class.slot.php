<?php
/**
 *
 */
class slot extends stat_object{
    /**
     * @var int
     */
    protected $_account_id;
    /**
     * @var int
     */
    protected $_player_slot;
    /**
     * @var int
     */
    protected $_hero_id;
    /**
     * @var int
     */
    protected $_item_0;
    /**
     * @var int
     */
    protected $_item_1;
    /**
     * @var int
     */
    protected $_item_2;
    /**
     * @var int
     */
    protected $_item_3;
    /**
     * @var int
     */
    protected $_item_4;
    /**
     * @var int
     */
    protected $_item_5;
    /**
     * @var int
     */
    protected $_kills;
    /**
     * @var int
     */
    protected $_deaths;
    /**
     * @var int
     */
    protected $_assists;
    /**
     * @var int
     */
    protected $_leaver_status;
    /**
     * @var int
     */
    protected $_gold;
    /**
     * @var int
     */
    protected $_last_hits;
    /**
     * @var int
     */
    protected $_denies;
    /**
     * @var int
     */
    protected $_gold_per_min;
    /**
     * @var int
     */
    protected $_xp_per_min;
    /**
     * @var int
     */
    protected $_gold_spent;
    /**
     * @var int
     */
    protected $_hero_damage;
    /**
     * @var int
     */
    protected $_tower_damage;
    /**
     * @var int
     */
    protected $_hero_healing;
    /**
     * @var int
     */
    protected $_level;
    /**
     * @var int
     */
    protected $_match_id;
    /**
     * @var array
     */
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
