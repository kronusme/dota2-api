<?php

use Dota2Api\Mappers\MatchesMapperWeb;

class MatchesMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $leagueid = 600;
        $expected_matches_count = 221; // without lan matches
        $mapper = new MatchesMapperWeb();
        $mapper->setLeagueId($leagueid);
        $matches = $mapper->load();
        $this->assertEquals((int)$mapper->getTotalMatches(), $expected_matches_count);
        $this->assertContainsOnlyInstancesOf('Dota2Api\Models\Match', $matches);
    }
}
