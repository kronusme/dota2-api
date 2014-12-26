<?php

use Dota2Api\Utils\Db;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Mappers\LeaguesMapperDb;
use Dota2Api\Mappers\LeaguePrizePoolMapperWeb;
use Dota2Api\Mappers\LeaguePrizePoolMapperDb;

class LeaguePrizePoolMapperDbTest extends PHPUnit_Framework_TestCase
{

    public $league_id = 600;

    public static function setUpBeforeClass()
    {
        $db = Db::obtain();
        $db->exec('DELETE FROM ' . Db::realTablename('league_prize_pools') . '');
        $leagues_mapper_web = new LeaguesMapperWeb();
        $leagues = $leagues_mapper_web->load();
        $leagues_mapper_db = new LeaguesMapperDb();
        $leagues_mapper_db->save($leagues[600]);
    }

    public static function tearDownAfterClass()
    {
        Db::obtain()->exec('DELETE FROM league_prize_pools');
        Db::obtain()->exec('DELETE FROM leagues');
    }

    public function testSaveLoad()
    {
        $league_prize_pool_mapper_web = new LeaguePrizePoolMapperWeb();
        $league_prize_pool_mapper_web->setLeagueId($this->league_id);
        $prize_pool_info = $league_prize_pool_mapper_web->load();

        $league_prize_pool_mapper_db = new LeaguePrizePoolMapperDb();
        $league_prize_pool_mapper_db->setLeagueId($this->league_id)->setPrizePool($prize_pool_info['prize_pool']);
        $league_prize_pool_mapper_db->save();

        $rows = $league_prize_pool_mapper_db->load();
        $this->assertEquals(1, count($rows));
    }

}
