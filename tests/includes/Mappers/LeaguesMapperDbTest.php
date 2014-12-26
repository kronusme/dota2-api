<?php

use Dota2Api\Mappers\LeaguesMapperDb;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Utils\Db;

class LeaguesMapperDbTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $mapperWeb = new LeaguesMapperWeb();
        $mapperDb = new LeaguesMapperDb();
        $leagues = $mapperWeb->load();
        $mapperDb->save($leagues[600]);
        $leaguesFromDb = $mapperDb->load();

        $this->assertEquals(1, count($leaguesFromDb));
        $mapperDb->save($leagues[65000]);
        $leaguesFromDb = $mapperDb->load();
        $this->assertEquals(2, count($leaguesFromDb));
    }

    protected function tearDown()
    {
        Db::obtain()->exec('DELETE FROM league_prize_pools');
        Db::obtain()->exec('DELETE FROM leagues');
    }
}
