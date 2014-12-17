<?php

use Dota2Api\Mappers\ItemsMapperWeb;

class ItemsMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $mapper = new ItemsMapperWeb();
        $items = $mapper->load();
        $this->assertGreaterThan(200, count($items));
        foreach($items as $id=>$item) {
            $this->assertTrue(is_string($item->get('name')) && strlen($item->get('name')) > 0);
            $this->assertTrue(is_string($item->get('localized_name')) && strlen($item->get('localized_name')) > 0);
        }
    }
}
