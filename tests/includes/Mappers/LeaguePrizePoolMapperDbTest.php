<?php

use Dota2Api\Utils\Db;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Mappers\LeaguesMapperDb;
use Dota2Api\Mappers\LeaguePrizePoolMapperWeb;
use Dota2Api\Mappers\LeaguePrizePoolMapperDb;

class LeaguePrizePoolMapperDbTest extends PHPUnit_Framework_TestCase
{

    public $leagueId = 600;

    public static function setUpBeforeClass()
    {
        $db = Db::obtain();
        $db->exec('DELETE FROM ' . Db::realTablename('league_prize_pools') . '');
        $leaguesMapperWeb = new LeaguesMapperWeb();
        $leagues = $leaguesMapperWeb->load();
        $leaguesMapperDb = new LeaguesMapperDb();
        $leaguesMapperDb->save($leagues[600]);
    }

    public static function tearDownAfterClass()
    {
        Db::obtain()->exec('DELETE FROM league_prize_pools');
        Db::obtain()->exec('DELETE FROM leagues');
    }

    public function testSaveLoad()
    {
        $leaguePrizePoolMapperWeb = new LeaguePrizePoolMapperWeb();
        $leaguePrizePoolMapperWeb->setLeagueId($this->leagueId);
        $prizePoolInfo = $leaguePrizePoolMapperWeb->load();

        $leaguePrizePoolMapperDb = new LeaguePrizePoolMapperDb();
        $leaguePrizePoolMapperDb->setLeagueId($this->leagueId)->setPrizePool($prizePoolInfo['prize_pool']);
        $leaguePrizePoolMapperDb->save();

        $rows = $leaguePrizePoolMapperDb->load();
        $this->assertEquals(1, count($rows));
    }

}
