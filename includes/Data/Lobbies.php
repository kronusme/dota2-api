<?php

namespace Dota2Api\Data;

/**
 * Information about game lobbies
 *
 * @author kronus
 * @example
 * <code>
 *   $lobbies = new Dota2Api\Data\Lobbies();
 *   $lobbies->parse();
 *   $lobbies->getFieldById(2, 'name'); // returns 'Tournament'
 * </code>
 */
class Lobbies extends Data
{
    public function __construct()
    {
        $this->_filename = 'lobbies.json';
        $this->_field = 'lobbies';
    }
}
