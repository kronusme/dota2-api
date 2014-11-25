<?php

class league_prize_pool_mapper_webTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $league_prize_pool_mapper_web = new league_prize_pool_mapper_web();
        $league_prize_pool_mapper_web->set_league_id(600);
        $prize_pool_info = $league_prize_pool_mapper_web->load();
        $this->assertTrue(intval($prize_pool_info['prize_pool']) > 0);
        $this->assertEquals(strval($prize_pool_info['league_id']), '600');
    }
}
