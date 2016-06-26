<?php

namespace Dota2Api\Models;

class LiveSlot extends Slot
{
    /**
     * @var int
     */
    protected $_ultimate_state;
    /**
     * @var int
     */
    protected $_ultimate_cooldown;
    /**
     * @var int
     */
    protected $_respawn_timer;
    /**
     * @var float
     */
    protected $_position_x;
    /**
     * @var float
     */
    protected $_position_y;
    /**
     * @var int
     */
    protected $_net_worth;
}
