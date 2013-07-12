<?php
/**
 * Load info about UGC objects
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $match_mapper_web = new match_mapper_web(37633163);
 *   $game = $match_mapper_web->load();
 *   $ugc_mapper_web = new ugc_mapper_web($game->get('radiant_logo'));
 *   $logo_data = $ugc_mapper_web->load();
 *   var_dump($logo_data);
 *   echo $logo_data->url;
 * </code>
 */
class ugc_mapper_web {

    /**
     * Request url
     */
    const steam_ugc_url = 'http://api.steampowered.com/ISteamRemoteStorage/GetUGCFileDetails/v1/';

    /**
     * @var int
     */
    private $_ugcid;

    public function __construct($ugcid) {
        $this->_ugcid = $ugcid;
    }

    /**
     * @param int $ugcid
     * @return object | null
     */
    public function load($ugcid = null) {
        if (!is_null($ugcid)) {
            $this->_ugcid = $ugcid;
        }
        $request = new request(
            self::steam_ugc_url,
            array('appid' => 570, 'ugcid' => $this->_ugcid)
        );
        return $request->send();

    }
}