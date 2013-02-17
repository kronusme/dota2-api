<?php
/**
 *
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
        $response = new SimpleXMLElement($response);
        $leagues_info = (array)($response->leagues);
        $leagues_info = $leagues_info['league'];
        $leagues = array();
        foreach($leagues_info as $league_info) {
            $info = (array)$league_info;
            $leagues[$info['leagueid']] = $info;
        }
        return $leagues;
    }
}
