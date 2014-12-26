<?php

namespace Dota2Api\Data;


/**
 * Information about items
 *
 * @author kronus
 * @package data
 * @example
 * <code>
 *   $items = new items();
 *   $items->parse();
 *   $items-getDataById(149); // get info about Crystalis
 *   $items->getImgUrlById(149, false); // large image
 *   $items->getImgUrlById(149); // thumb
 * </code>
 */
class Items extends HeroesData
{
    /**
     * Empty slot has id = 0
     */
    const empty_id = 0;

    public function __construct()
    {
        $this->setFilename('items.json');
        $this->setField('items');
    }
}
