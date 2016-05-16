<?php

namespace Dota2Api\Data;

/**
 * Information about heroes abilities
 *
 * @author kronus
 * @example
 * <code>
 *   $abilities = new Dota2Api\Data\Abilities();
 *   $abilities->parse();
 *   $abilities->getDataById(5172); // return array for ability with id 5172 (BeastMaster Inner Beast)
 *   // same, because there are no thumbs for abilities
 *   $abilities->getImgUrlById(5172, false);
 *   $abilities->getImgUrlById(5172);
 * </code>
 */
class Abilities extends HeroesData
{
    /**
     * Stats ability identifier
     */
    const STATS_ABILITY_ID = 5002;

    public function __construct()
    {
        $this->setFilename('abilities.json');
        $this->setField('abilities');
        // no small images for abilities :(
        $this->_suffixes['thumb'] = 'lg';
    }

    public function getImgUrlById($id, $thumb = true)
    {
        return ($id !== self::STATS_ABILITY_ID) ? parent::getImgUrlById($id, $thumb) : 'images/stats.png';
    }
	
	/**
	* Returns the Cooldown of an Ability at a specific level
	*
	* @param int $id item identifier
	* @param int $level level of the ability
	* @return float (-1 if ability not found, -2 if ability doesn't have a cooldown at the given level)
	*/
	public function getCooldownById($id, $level)
	{
		$level = (int)$level;
		$data = $this->getDataById($id);
        if (null === $data) {
            return -1;
        } else if($level < 0 || $level >= count($data['cooldown'])){
			return -2;
		} else {
			return $data['cooldown'][$level];
		}
	}
	
	/**
	* Returns the Cooldowns of an Ability
	*
	* @param int $id item identifier
	* @return array with floats or null if ability wasn't found
	*/
	public function getCooldownsById($id)
	{
		$level = (int)$level;
		$data = $this->getDataById($id);
        if (null === $data) {
            return null;
		} else {
			return $data['cooldown'];
		}
	}
	
	/**
	* Returns if an Ability is an Ultimate
	*
	* @param int $id item identifier
	* @return bool
	*/
	public function isUltimate($id) {
		$data = $this->getDataById($id);
		$id = int($id);
		
		if (null === $data) {
            return false;
		} 
		return $data['ultimate'];
	}
}
