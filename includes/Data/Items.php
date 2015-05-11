<?php

namespace Dota2Api\Data;

/**
 * Information about items
 *
 * @author kronus
 * @example
 * <code>
 *   $items = new Dota2Api\Data\Items();
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
    const EMPTY_ID = 0;

    /**
     * Some items where replaced in 6.84-patch
     * Example:
     * before 6.84 - mystery_hook was 217
     * 6.84+ - mystery_hook is 1001
     * @var array (old item id => new item id)
     */
    protected $_conflictsMap = array(
        217 => 1001,
        218 => 1002,
        219 => 1003,
        220 => 1004,
        221 => 1005,
        226 => 1006,
        228 => 1007,
        235 => 1008,
        227 => 1009,
        229 => 1010,
        230 => 1011,
        231 => 1012,
        232 => 1013,
        233 => 1014,
        234 => 1015,
        236 => 1016,
        237 => 1017,
        238 => 1018,
        239 => 1019,
        240 => 1020
    );

    public function __construct()
    {
        $this->setFilename('items.json');
        $this->setField('items');
    }

    public function getImgUrlById($id, $thumb = true, $isConflict = false)
    {
        $id = (int)$id;
        if ($isConflict) {
            $id = array_key_exists($id, $this->_conflictsMap) ? $this->_conflictsMap[$id] : $id;
        }
        return parent::getImgUrlById($id, $thumb);
    }
}
