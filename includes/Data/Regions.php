<?php

namespace Dota2Api\Data;

/**
 * Information about servers regions
 *
 * @author kronus
 * @package data
 * @example
 * <code>
 *   $regions = new regions();
 *   $regions->parse();
 *   $regions->getFieldById(132, 'name'); // returns 'Europe West'
 * </code>
 */
class Regions extends Data
{
    public function __construct()
    {
        $this->_filename = 'regions.json';
        $this->_field = 'regions';
    }
}
