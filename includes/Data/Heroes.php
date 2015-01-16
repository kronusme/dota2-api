<?php

namespace Dota2Api\Data;

/**
 * Information about heroes (id, name, localized name)
 *
 * @author kronus
 * @example
 * <code>
 *   $heroes = new Dota2Api\Data\Heroes();
 *   $heroes->parse();
 *   $heroes-getDataById(97); // get info about Magnus
 *   $heroes->getImgUrlById(97, false); // large image
 *   $heroes->getImgUrlById(97); // thumb
 * </code>
 */
class Heroes extends HeroesData
{
    public function __construct()
    {
        $this->setFilename('heroes.json');
        $this->setField('heroes');
        //$this->_suffixes['thumb'] = 'sb';
    }
}
