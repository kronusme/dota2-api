<?php
/**
 * Used with player_mapper_db
 * All Info About One Player
 */
 class player_db extends stat_object {
	 /**
     * This id used when some player select don't show personal statistic
     */
    const ANONYMOUS = 4294967295;
	/**
	 * Used for db class
	 * @var string
	 */
	protected $_steam_id;
    /**
     * @var int
     */
    protected $_community_visibility_state;
    /**
     * @var int
     */
    protected $_profile_state;
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
    protected $_comment_permission;
    /**
     * @var string
     */
    protected $_profile_url;
    /**
     * @var string
     */
    protected $_avatar;
    /**
     * @var string
     */
    protected $_avatar_medium;
    /**
     * @var string
     */
    protected $_avatar_full;
    /**
     * @var int
     */
    protected $_personastate;
    /**
     * @var string
     */
    protected $_real_name;
    /**
     * @var string
     */
    protected $_primary_clan_id;
    /**
     * @var timestamp
     */
    protected $_time_created;

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
?>
