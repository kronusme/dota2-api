<?php
/**
 * Load info about leagues
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *  $leagues_mapper = new leagues_mapper();
 *  $leagues = $leagues_mapper->load();
 *  foreach($leagues as $league) {
 *    echo $league['description'];
 *    if ($league['tournament_url']) {
 *      echo $league['tournament_url'];
 *    }
 *  }
 * </code>
 */
class leagues_mapper {
    /**
     *
     */
    const leagues_steam_url = 'https://api.steampowered.com/IDOTA2Match_570/GetLeagueListing/v0001/';

    /**
     *
     */
    public function __construct(){}

    /**
     * @return array
     */
    public function load() {
        $request = new request(
            self::leagues_steam_url,
            array()
        );
        $response = $request->send();
        if (is_null($response)) {
            return null;
        }
        $leagues_info = (array)($response->leagues);
        $leagues_info = $leagues_info['league'];
        $leagues = array();
        foreach($leagues_info as $league_info) {
            $info = (array)$league_info;
            array_walk($info, function (&$v) {
                $v = (string)$v;
            });
            $leagues[$info['leagueid']] = $info;
        }
        return $leagues;
    }
}
