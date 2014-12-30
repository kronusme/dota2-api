<?php

namespace Dota2Api\Models;

class League extends StatObject
{
    /**
     * League identifier
     * @var int
     */
    protected $_leagueid;
    /**
     * League name
     * @var string
     */
    protected $_name;
    /**
     * League description
     * @var string
     */
    protected $_description;
    /**
     * League home url
     * @var string
     */
    protected $_tournament_url;
    /**
     * ????????
     * @var int
     */
    protected $_itemdef;
    /**
     * Is league finished
     * @var bool
     */
    protected $_is_finished = false;
}
