<?php

class leagues_mapper_dbTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $mapper_web = new leagues_mapper_web();
        $mapper_db = new leagues_mapper_db();
        $leagues = $mapper_web->load();
        $mapper_db->save($leagues[600]);
        $leagues_from_db = $mapper_db->load();

        $this->assertEquals(1, count($leagues_from_db));
        $mapper_db->save($leagues[65000]);
        $leagues_from_db = $mapper_db->load();
        $this->assertEquals(2, count($leagues_from_db));
    }

    protected function tearDown() {
        db::obtain()->exec('DELETE FROM league_prize_pools');
        db::obtain()->exec('DELETE FROM leagues');
    }
}
