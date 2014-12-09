<?php

use Dota2Api\Mappers\MatchesMapperWeb;

class MatchesMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $leagueid = 600;
        $expected_matches_count = 221; // without lan matches
        $mapper = new MatchesMapperWeb();
        $mapper->set_league_id($leagueid);
        $matches = $mapper->load();
        $this->assertEquals((int)$mapper->get_total_matches(), $expected_matches_count);
        $this->assertContainsOnlyInstancesOf('Dota2Api\Models\Match', $matches);
    }
}
