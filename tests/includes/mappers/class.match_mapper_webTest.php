<?php

class match_mapper_webTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $match_id = '683300315';
        $expected_match_info = array(
            'game_mode' => '2',
            'start_time' => '2014-05-27 13:40:06',
            'radiant_win' => '1',
            'first_blood_time' => '7',
            'leagueid' => '600',
            'duration' => '1662',
        );
        $mapper = new match_mapper_web($match_id);
        $match = $mapper->load();
        $this->assertEquals(get_class($match), 'match');
        foreach($expected_match_info as $k=>$v) {
            $this->assertEquals($match->get($k), $v);
        }

        $expected_slots_info = array(
            0 => array(
                'ability_upgrades' => 13,
                'level' => 13,
            ),
            1 => array(
                'ability_upgrades' => 16,
                'level' => 16,
            ),
            2 => array(
                'ability_upgrades' => 15,
                'level' => 15,
            ),
            3 => array(
                'ability_upgrades' => 17, // player got level up but didn't choose any spell
                'level' => 18,
            ),
            4 => array(
                'ability_upgrades' => 14,
                'level' => 14,
            ),
            128 => array(
                'ability_upgrades' => 11,
                'level' => 11,
            ),
            129 => array(
                'ability_upgrades' => 16,
                'level' => 16,
            ),
            130 => array(
                'ability_upgrades' => 13,
                'level' => 13,
            ),
            131 => array(
                'ability_upgrades' => 13,
                'level' => 13,
            ),
            132 => array(
                'ability_upgrades' => 11,
                'level' => 11,
            )
        );
        $slots = $match->get_all_slots();
        foreach($expected_slots_info as $slot_id=>$slot) {
            $this->assertTrue($slots[$slot_id]->get('account_id') !== player::ANONYMOUS);
            $this->assertEquals($slot['level'], (int)$slots[$slot_id]->get('level'));
            $this->assertEquals($slot['ability_upgrades'], count($slots[$slot_id]->get_abilities_upgrade()));
        }

        $picks_bans = $match->get_all_picks_bans();
        $this->assertInternalType('array', $picks_bans);
        $fl = true;
        foreach($picks_bans as $r) {
            if (!in_array($r['is_pick'], array('1', '0'))) $fl = false;
        }
        $this->assertTrue($fl);
    }

}
