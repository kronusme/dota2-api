<?php

namespace Dota2Api\Data;


/**
 * Information about heroes abilities
 *
 * @author kronus
 * @package data
 * @example
 * <code>
 *   $abilities = new abilities();
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
    const stats_ability_id = 5002;

    public function __construct()
    {
        $this->setFilename('abilities.json');
        $this->setField('abilities');
        // no small images for abilities :(
        $this->_suffixes['thumb'] = 'lg';
    }

    public function getImgUrlById($id, $thumb = true)
    {
        if ($id != self::stats_ability_id) {
            return parent::getImgUrlById($id, $thumb);
        }
        return 'images/stats.png';
    }
}