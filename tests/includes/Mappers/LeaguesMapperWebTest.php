<?php

use Dota2Api\Mappers\LeaguesMapperWeb;

class LeaguesMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $mapper = new LeaguesMapperWeb();
        $leagues = $mapper->load();
        $this->assertInternalType('array', $leagues);
        $this->assertGreaterThan(0, count($leagues));
    }
}
