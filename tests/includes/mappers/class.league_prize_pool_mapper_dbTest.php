<?php

class league_prize_pool_mapper_dbTest extends PHPUnit_Framework_TestCase
{

    public $league_id = 600;

    public static function setUpBeforeClass () {
        $db = db::obtain();
        $db->exec('DELETE FROM '.db::real_tablename('league_prize_pools').'');
        $leagues_mapper_web = new leagues_mapper_web();
        $leagues = $leagues_mapper_web->load();
        $leagues_mapper_db = new leagues_mapper_db();
        $leagues_mapper_db->save($leagues[600]);
    }

    public static function tearDownAfterClass () {
        db::obtain()->exec('DELETE FROM league_prize_pools');
        db::obtain()->exec('DELETE FROM leagues');
    }

    public function testSaveLoad() {
        $league_prize_pool_mapper_web = new league_prize_pool_mapper_web();
        $league_prize_pool_mapper_web->set_league_id($this->league_id);
        $prize_pool_info = $league_prize_pool_mapper_web->load();

        $league_prize_pool_mapper_db = new league_prize_pool_mapper_db();
        $league_prize_pool_mapper_db->set_league_id($this->league_id)->set_prize_pool($prize_pool_info['prize_pool']);
        $league_prize_pool_mapper_db->save();

        $rows = $league_prize_pool_mapper_db->load();
        $this->assertEquals(1, count($rows));
    }

}
