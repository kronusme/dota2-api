<?php

namespace Dota2Api\Mappers;

use Dota2Api\Models\Item;
use Dota2Api\Utils\Db;

/**
 * Load info about in game items from db
 *
 * @example
 * <code>
 *  $itemsMapperDb = new Dota2Api\Mappers\ItemsMapperDb();
 *  $itemsInfo = $itemsMapperDb->load();
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
class ItemsMapperDb extends ItemsMapper
{

    /**
     * @return Item[]
     */
    public function load()
    {
        $db = Db::obtain();
        $items = array();
        $itemsInfo = $db->fetchArrayPDO('SELECT * FROM ' . Db::realTablename('items'));
        foreach ($itemsInfo as $itemInfo) {
            $item = new Item();
            $item->setArray($itemInfo);
            $items[$itemInfo['id']] = $item;
        }
        return $items;
    }

    /**
     * @param Item[] $data list of items
     */
    public function save(array $data)
    {
        $db = Db::obtain();
        $dataToInsert = array();
        foreach ($data as $item) {
            array_push($dataToInsert, $item->getDataArray());
        }
        $db->insertManyPDO(
            Db::realTablename('items'),
            array('id', 'name', 'cost', 'secret_shop', 'side_shop', 'recipe', 'localized_name'),
            $dataToInsert,
            true
        );
    }
}
