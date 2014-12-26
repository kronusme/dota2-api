<?php

use Dota2Api\Mappers\HeroesMapper;

class HeroesMapperTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $mapper = new HeroesMapper();
        $heroes = $mapper->load();
        $this->assertGreaterThan(100, count($heroes));
        foreach ($heroes as $id => $hero) {
            $this->assertTrue(is_string($hero['name']) && strlen($hero['name']) > 0);
            $this->assertTrue(is_string($hero['localized_name']) && strlen($hero['localized_name']) > 0);
        }
    }
}
