<?php

use Dota2Api\Mappers\LeaguesMapperDb;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Utils\Db;

class LeaguesMapperDbTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $mapper_web = new LeaguesMapperWeb();
        $mapper_db = new LeaguesMapperDb();
        $leagues = $mapper_web->load();
        $mapper_db->save($leagues[600]);
        $leagues_from_db = $mapper_db->load();

        $this->assertEquals(1, count($leagues_from_db));
        $mapper_db->save($leagues[65000]);
        $leagues_from_db = $mapper_db->load();
        $this->assertEquals(2, count($leagues_from_db));
    }

    protected function tearDown() {
        Db::obtain()->exec('DELETE FROM league_prize_pools');
        Db::obtain()->exec('DELETE FROM leagues');
    }
}
