<?php

use Dota2Api\Mappers\MatchMapperWeb;
use Dota2Api\Models\Player;

class MatchMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $matchId = '2159643824';
        $expectedMatchInfo = array(
            'game_mode' => '2',
            'radiant_win' => '1',
            'first_blood_time' => '77',
            'leagueid' => '4395',
            'duration' => '1301'
        );
        $mapper = new MatchMapperWeb($matchId);
        $match = $mapper->load();
        while (!$match) {
            $match = $mapper->load();
        }
        $this->assertInstanceOf('Dota2Api\Models\Match', $match);
        foreach ($expectedMatchInfo as $k => $v) {
            $this->assertEquals($match->get($k), $v);
        }

        $expectedSlotsInfo = array(
            0 => array(
                'ability_upgrades' => 15,
                'level' => 15
            ),
            1 => array(
                'ability_upgrades' => 11,
                'level' => 11
            ),
            2 => array(
                'ability_upgrades' => 8,
                'level' => 8
            ),
            3 => array(
                'ability_upgrades' => 11,
                'level' => 11
            ),
            4 => array(
                'ability_upgrades' => 14,
                'level' => 14
            ),
            128 => array(
                'ability_upgrades' => 9,
                'level' => 9
            ),
            129 => array(
                'ability_upgrades' => 10,
                'level' => 10
            ),
            130 => array(
                'ability_upgrades' => 13,
                'level' => 13
            ),
            131 => array(
                'ability_upgrades' => 8,
                'level' => 10
            ),
            132 => array(
                'ability_upgrades' => 13,
                'level' => 13
            )
        );
        $slots = $match->getAllSlots();
        foreach ($expectedSlotsInfo as $slotId => $slot) {
            $this->assertTrue($slots[$slotId]->get('account_id') !== Player::ANONYMOUS);
            $this->assertEquals($slot['level'], (int)$slots[$slotId]->get('level'));
            $this->assertEquals($slot['ability_upgrades'], count($slots[$slotId]->getAbilitiesUpgrade()));
        }

        $picksBans = $match->getAllPicksBans();
        $this->assertInternalType('array', $picksBans);
        $this->assertGreaterThan(0, count($picksBans));
        $fl = true;
        foreach ($picksBans as $r) {
            if (!in_array($r['is_pick'], array('1', '0'), true)) {
                $fl = false;
            }
        }
        $this->assertTrue($fl);
    }

}
