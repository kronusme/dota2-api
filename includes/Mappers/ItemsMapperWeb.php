<?php

namespace Dota2Api\Mappers;

use Dota2Api\Models\Item;
use Dota2Api\Utils\Request;

/**
 * Load info about in game items
 *
 * @example
 * <code>
 *  $itemsMapperWeb = new Dota2Api\Mappers\ItemsMapperWeb();
 *  $itemsInfo = $itemsMapperWeb->load();
 *  print_r($itemsInfo);
 *  foreach($itemsInfo as $item) {
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
class ItemsMapperWeb extends ItemsMapper
{

    /**
     * Request url
     */
    const ITEMS_STEAM_URL = 'https://api.steampowered.com/IEconDOTA2_570/GetGameItems/v0001/';

    /**
     * @return Item[]
     */
    public function load()
    {
        $request = new Request(
            self::ITEMS_STEAM_URL,
            array()
        );
        $response = $request->send();
        if (null === $response) {
            return null;
        }
        $itemsInfo = (array)($response->items);
        $itemsInfo = $itemsInfo['item'];
        $items = array();
        foreach ($itemsInfo as $itemInfo) {
            $info = (array)$itemInfo;
            array_walk($info, function (&$v) {
                $v = (string)$v;
            });
            $item = new item();
            $item->setArray($info);
            $items[$info['id']] = $item;
        }
        return $items;
    }
}
