<?php

use Dota2Api\Mappers\LeaguePrizePoolMapperWeb;

class LeaguePrizePoolMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $league_prize_pool_mapper_web = new LeaguePrizePoolMapperWeb();
        $league_prize_pool_mapper_web->setLeagueId(600);
        $prize_pool_info = $league_prize_pool_mapper_web->load();
        $this->assertTrue(intval($prize_pool_info['prize_pool']) >= 0);
        $this->assertEquals('600', strval($prize_pool_info['league_id']));
    }
}
