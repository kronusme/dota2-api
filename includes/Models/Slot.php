<?php

namespace Dota2Api\Models;

/**
 * Class for all information about one slot (includes ability upgrades if provided)
 *
 * @author kronus
 */
class Slot extends StatObject
{
    /**
     * inner identifier used in local queries (web mappers don't feel it!)
     * @var int
     */
    protected $_id;
    /**
     * account_id - the player's 32-bit Steam ID - will be set to "4294967295" if the player has set their account to private.
     * @var int
     */
    protected $_account_id;
    /**
     * an 8-bit unsigned int: if the left-most bit is set, the player was on dire. the two right-most bits represent the player slot (0-4)
     * @var int
     */
    protected $_player_slot;
    /**
     * the numeric ID of the hero that the player used.
     * @var int
     */
    protected $_hero_id;
    /**
     * the numeric ID of the item that player finished with in their top-left slot.
     * @var int
     */
    protected $_item_0;
    /**
     * the numeric ID of the item that player finished with in their top-center slot.
     * @var int
     */
    protected $_item_1;
    /**
     * the numeric ID of the item that player finished with in their top-right slot.
     * @var int
     */
    protected $_item_2;
    /**
     * the numeric ID of the item that player finished with in their bottom-left slot.
     * @var int
     */
    protected $_item_3;
    /**
     * the numeric ID of the item that player finished with in their bottom-center slot.
     * @var int
     */
    protected $_item_4;
    /**
     * the numeric ID of the item that player finished with in their bottom-right slot.
     * @var int
     */
    protected $_item_5;
    /**
     * the number of kills the player got.
     * @var int
     */
    protected $_kills;
    /**
     * the number of times the player died.
     * @var int
     */
    protected $_deaths;
    /**
     * the number of assists the player got.
     * @var int
     */
    protected $_assists;
    /**
     * NULL - player is a bot.
     * 2 - player abandoned game.
     * 1 - player left game after the game has become safe to leave.
     * 0 - Player stayed for the entire match.
     *
     * @var int
     */
    protected $_leaver_status;
    /**
     * the amount of gold the player had left at the end of the match
     * @var int
     */
    protected $_gold;
    /**
     * the number of times a player last-hit a creep
     * @var int
     */
    protected $_last_hits;
    /**
     * the number of times a player denied a creep
     * @var int
     */
    protected $_denies;
    /**
     * the player's total gold/min
     * @var int
     */
    protected $_gold_per_min;
    /**
     * the player's total xp/min
     * @var int
     */
    protected $_xp_per_min;
    /**
     * the total amount of gold the player spent over the entire match
     * @var int
     */
    protected $_gold_spent;
    /**
     * the amount of damage the player dealt to heroes
     * @var int
     */
    protected $_hero_damage;
    /**
     * the amount of damage the player dealt to towers
     * @var int
     */
    protected $_tower_damage;
    /**
     * the amount of damage on other players that the player healed
     * @var int
     */
    protected $_hero_healing;
    /**
     * the player's final level
     * @var int
     */
    protected $_level;
    /**
     * the numeric match ID
     * @var int
     */
    protected $_match_id;
    /**
     * Array of ability upgrades for player in this slot (can be empty)
     * @var array
     */
    protected $_abilities_upgrade = array();

    /**
     * Array of items ids of the units like Spirit Bear
     * @var array
     */
    protected $_additional_unit_items = array();

    /**
     * Set array of ability upgrades (used in mappers)
     * @param array $data
     * @return slot
     */
    public function setAbilitiesUpgrade($data)
    {
        $this->_abilities_upgrade = $data;
        return $this;
    }

    /**
     * Return array of ability upgrades
     * @return array
     */
    public function getAbilitiesUpgrade()
    {
        return $this->_abilities_upgrade;
    }

    /**
     * Set array of additional unit items ids
     * @param array $data
     * @return slot
     */
    public function setAdditionalUnitItems(array $data)
    {
        $this->_additional_unit_items = $data;
        return $this;
    }

    /**
     * Get array of additional unit items ids
     * @return array
     */
    public function getAdditionalUnitItems()
    {
        return $this->_additional_unit_items;
    }

    /**
     * Remove item from additional unit items
     * @param int $item_id
     * @return slot
     */
    public function removeAdditionalUnitItem($item_id)
    {
        foreach ($this->_additional_unit_items as $k => $id) {
            if ($id === $item_id) {
                unset($this->_additional_unit_items[$k]);
                break;
            }
        }
        return $this;
    }

    /**
     * Just empty construct
     * Don't use me directly!
     */
    public function __construct()
    {

    }
}
