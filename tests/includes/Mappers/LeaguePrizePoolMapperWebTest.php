<?php

use Dota2Api\Mappers\LeaguePrizePoolMapperWeb;

class LeaguePrizePoolMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $leaguePrizePoolMapperWeb = new LeaguePrizePoolMapperWeb();
        $leaguePrizePoolMapperWeb->setLeagueId(600);
        $prizePoolInfo = $leaguePrizePoolMapperWeb->load();
        $this->assertTrue((int)$prizePoolInfo['prize_pool'] >= 0);
        $this->assertEquals('600', (string)$prizePoolInfo['league_id']);
    }
}
