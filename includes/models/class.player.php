<?php
/**
 * All info about one player
 *
 * @author kronus
 * @package models
 */
class player extends stat_object {
    /**
     * This id used when some player select don't show personal statistic
     */
    const ANONYMOUS = 4294967295;
    /**
     * @var string
     */
    protected $_steamid;
    /**
     * @var int
     */
    protected $_communityvisibilitystate;
    /**
     * @var int
     */
    protected $_profilestate;
    /**
     * @var string
     */
    protected $_personaname;
    /**
     * @var timestamp
     */
    protected $_lastlogoff;
    /**
     * @var int
     */
    protected $_commentpermission;
    /**
     * @var string
     */
    protected $_profileurl;
    /**
     * @var string
     */
    protected $_avatar;
    /**
     * @var string
     */
    protected $_avatarmedium;
    /**
     * @var string
     */
    protected $_avatarfull;
    /**
     * @var int
     */
    protected $_personastate;
    /**
     * @var string
     */
    protected $_realname;
    /**
     * @var string
     */
    protected $_primaryclanid;
    /**
     * @var timestamp
     */
    protected $_timecreated;

    /**
     *
     */
    public function __construct(){}
    /**
     * Convert DotA2 user id to Steam ID
     * @param string $id
     * @return string
     */
    public static function convert_id($id) {
        if (strlen($id) === 17) {
            $converted = substr($id, 3) - 61197960265728;
        }
        else {
            $converted = '765'.($id + 61197960265728);
        }
        return (string) $converted;
    }
}