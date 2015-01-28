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
            $this->assertTrue(is_string($hero['name']) && '' !== $hero['name']);
            $this->assertTrue(is_string($hero['localized_name']) && '' !== $hero['localized_name']);
        }
    }
}
