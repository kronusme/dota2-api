<?php

use Dota2Api\Mappers\MatchMapperWeb;
use Dota2Api\Models\Player;

class match_mapper_webTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $matchId = '683300315';
        $expectedMatchInfo = array(
            'game_mode' => '2',
            'radiant_win' => '1',
            'first_blood_time' => '7',
            'leagueid' => '600',
            'duration' => '1662'
        );
        $mapper = new MatchMapperWeb($matchId);
        $match = $mapper->load();
        $this->assertInstanceOf('Dota2Api\Models\Match', $match);
        foreach ($expectedMatchInfo as $k => $v) {
            $this->assertEquals($match->get($k), $v);
        }

        $expectedSlotsInfo = array(
            0 => array(
                'ability_upgrades' => 13,
                'level' => 13
            ),
            1 => array(
                'ability_upgrades' => 16,
                'level' => 16
            ),
            2 => array(
                'ability_upgrades' => 15,
                'level' => 15
            ),
            3 => array(
                'ability_upgrades' => 17, // player got level up but didn't choose any spell
                'level' => 18
            ),
            4 => array(
                'ability_upgrades' => 14,
                'level' => 14
            ),
            128 => array(
                'ability_upgrades' => 11,
                'level' => 11
            ),
            129 => array(
                'ability_upgrades' => 16,
                'level' => 16
            ),
            130 => array(
                'ability_upgrades' => 13,
                'level' => 13
            ),
            131 => array(
                'ability_upgrades' => 13,
                'level' => 13
            ),
            132 => array(
                'ability_upgrades' => 11,
                'level' => 11
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
        $fl = true;
        foreach ($picksBans as $r) {
            if (!in_array($r['is_pick'], array('1', '0'), true)) {
                $fl = false;
            }
        }
        $this->assertTrue($fl);
    }

}
