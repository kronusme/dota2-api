<?php

use Dota2Api\Data\Items;

class ItemsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Items
     */
    public $items;

    public function setUp()
    {
        $this->items = new Items();
        $this->items->parse();
    }

    public function testGetImgUrlById()
    {
        $url = $this->items->getImgUrlById(220, true, true);
        $this->assertContains('mystery_toss', $url);

        $url = $this->items->getImgUrlById(220, true, false);
        $this->assertContains('travel_boots_2', $url);
    }
}