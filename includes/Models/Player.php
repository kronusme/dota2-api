<?php

namespace Dota2Api\Models;

/**
 * All info about one player
 *
 * @author kronus
 */
class Player extends StatObject
{
    /**
     * This id used when some player select don't show personal statistic
     */
    const ANONYMOUS = 4294967295;
    /**
     * @var string
     */
    protected $_steamid;
    /**
     * @var string
     */
    protected $_personaname;
    /**
     * @var string
     */
    protected $_profileurl;
    /**
     * @var string
     */
    protected $_avatar;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * Convert DotA2 user id to Steam ID
     * @param string $id
     * @return string
     */
    public static function convertId($id)
    {
        if (strlen($id) === 17) {
            $converted = substr($id, 3) - 61197960265728;
        } else {
            $converted = '765' . ($id + 61197960265728);
        }
        return (string)$converted;
    }
}
