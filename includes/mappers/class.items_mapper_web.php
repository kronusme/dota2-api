<?php

/**
 * Load info about in game items
 *
 * @example
 * <code>
 *  $items_mapper_web = new items_mapper_web();
 *  $items_info = $items_mapper_web->load();
 *  print_r($items_info);
 *  foreach($items_info as $item) {
 *      echo $item->get('id');
 *      echo $item->get('name');
 *      echo $item->get('cost');
 *      echo $item->get('secret_shop');
 *      echo $item->get('side_shop');
 *      echo $item->get('recipe');
 *      echo $item->get('localized_name');
 *  }
 * </code>
 */
class items_mapper_web extends items_mapper {

    /**
     * Request url
     */
    const items_steam_url = 'https://api.steampowered.com/IEconDOTA2_570/GetGameItems/v0001/';

    public function __construct(){}

    /**
     * @return array
     */
    public function load() {
        $request = new request(
            self::items_steam_url,
            array()
        );
        $response = $request->send();
        if (is_null($response)) {
            return null;
        }
        $items_info = (array)($response->items);
        $items_info = $items_info['item'];
        $items = array();
        foreach($items_info as $item_info) {
            $info = (array)$item_info;
            array_walk($info, function (&$v) {
                $v = (string)$v;
            });
            $item = new item();
            $item->set_array($info);
            $items[$info['id']] = $item;
        }
        return $items;
    }

}