<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;

/**
 * Load info about UGC objects
 * @author kronus
 * @example
 * <code>
 *   $matchMapperWeb = new Dota2Api\Mappers\MatchMapperWeb(37633163);
 *   $game = $matchMapperWeb->load();
 *   $ugcMapperWeb = new Dota2Api\Mappers\UgcMapperWeb($game->get('radiant_logo'));
 *   $logo_data = $ugcMapperWeb->load();
 *   var_dump($logoData);
 *   echo $logoData->url;
 * </code>
 */
class UgcMapperWeb
{

    /**
     * Request url
     * @var string
     */
    const STEAM_UGC_URL = 'http://api.steampowered.com/ISteamRemoteStorage/GetUGCFileDetails/v1/';

    /**
     * @var int
     */
    private $_ugcid;

    public function __construct($ugcid = null)
    {
        $this->_ugcid = $ugcid;
    }

    /**
     * @param int $ugcid
     * @return object | null
     */
    public function load($ugcid = null)
    {
        if (null !== $ugcid) {
            $this->_ugcid = $ugcid;
        }
        $request = new Request(
            self::STEAM_UGC_URL,
            array('appid' => 570, 'ugcid' => $this->_ugcid)
        );
        return $request->send();

    }
}
