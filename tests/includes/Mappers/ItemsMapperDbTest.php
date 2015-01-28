<?php

use Dota2Api\Mappers\ItemsMapperDb;
use Dota2Api\Models\Item;

class ItemsMapperDbTest extends PHPUnit_Framework_TestCase
{

    public function testLoad()
    {

        $itemsMapperDb = new ItemsMapperDb();
        $items = $itemsMapperDb->load();
        foreach ($items as $item) {
            $this->assertTrue($item instanceof Item);
            $this->assertTrue('' !== $item->get('localized_name'));
            $this->assertTrue('' !== $item->get('name'));
        }

    }

}