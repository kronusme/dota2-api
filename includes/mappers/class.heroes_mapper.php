<?php
/**
 * Load basic info about DotA 2 heroes
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $heroes_mapper = new heroes_mapper();
 *   $heroes = $heroes_mapper->load();
 *   print_r($heroes);
 * </code>
 */
class heroes_mapper {
    /**
     * Request url
     */
    const heroes_steam_url = 'https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/';

    /**
     * @param string $lang
     */
    public function __construct($lang = null) {
        if (!is_null($lang)) {
            $this->set_language($lang);
        }
    }

    /**
     * @return array
     */
    public function load() {
        $request = new request(
            self::heroes_steam_url,
            array()
        );
        $response = $request->send();
        if (is_null($response)) {
            return null;
        }
        $heroes_info = (array)($response->heroes);
        $heroes_info = $heroes_info['hero'];
        $heroes = array();
        foreach($heroes_info as $hero_info) {
            $info = (array)$hero_info;
            $heroes[$info['id']] = $info;
        }
        return $heroes;
    }
}
