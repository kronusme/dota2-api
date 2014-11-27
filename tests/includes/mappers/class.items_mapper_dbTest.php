<?php

class items_mapper_dbTest extends PHPUnit_Framework_TestCase
{

    public function testLoad () {

        $items_mapper_db = new items_mapper_db();
        $items = $items_mapper_db->load();
        foreach($items as $item) {
            $this->assertTrue($item instanceof item);
            $this->assertTrue(strlen($item->get('localized_name')) > 0);
            $this->assertTrue(strlen($item->get('name')) > 0);
        }

    }

}