<?php

namespace Dota2Api\Data;

/**
 * Information about game modes
 *
 * @author kronus
 * @example
 * <code>
 *   $mods = new Dota2Api\Data\Mods();
 *   $mods->parse();
 *   $mods->getFieldById(1, 'name'); // returns 'All Pick'
 * </code>
 */
class Mods extends Data
{
    public function __construct()
    {
        $this->_filename = 'mods.json';
        $this->_field = 'mods';
    }
}
