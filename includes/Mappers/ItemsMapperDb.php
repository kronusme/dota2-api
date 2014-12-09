<?php

namespace Dota2Api\Mappers;

use Dota2Api\Models\Item;
use Dota2Api\Utils\Db;

/**
 * Load info about in game items from db
 *
 * @example
 * <code>
 *  $items_mapper_db = new items_mapper_db();
 *  $items_info = $items_mapper_db->load();
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
class ItemsMapperDb extends ItemsMapper {

    /**
     * @return Item[]
     */
    public function load() {
        $db = Db::obtain();
        $items = array();
        $items_info = $db->fetch_array_pdo('SELECT * FROM '.Db::real_tablename('items'));
        foreach($items_info as $item_info) {
            $item = new Item();
            $item->set_array($item_info);
            $items[$item_info['id']] = $item;
        }
        return $items;
    }

    /**
     * @param Item[] $data list of items
     */
    public function save(array $data) {
        $db = Db::obtain();
        $data_to_insert = array();
        foreach($data as $item) {
            array_push($data_to_insert, $item->get_data_array());
        }
        $db->insert_many_pdo(Db::real_tablename('items'), array('id', 'name', 'cost', 'secret_shop', 'side_shop', 'recipe', 'localized_name'), $data_to_insert, true);
    }

}