<?php

class items_mapper_webTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $mapper = new items_mapper_web();
        $items = $mapper->load();
        $this->assertGreaterThan(200, count($items));
        foreach($items as $id=>$item) {
            $this->assertTrue(is_string($item->name) && strlen($item->name) > 0);
            $this->assertTrue(is_string($item->localized_name) && strlen($item->localized_name) > 0);
        }
    }
}
