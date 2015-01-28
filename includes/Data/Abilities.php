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
}
