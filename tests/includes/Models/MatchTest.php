<?php

use Dota2Api\Models\Match;
use Dota2Api\Mappers\MatchMapperWeb;

class MatchTest extends PHPUnit_Framework_TestCase
{

    public $matchId = 1697818230;

    /**
     * @var Match
     */
    public $match;

    public function setUp()
    {
        $matchMapperWeb = new MatchMapperWeb($this->matchId);
        $this->match = $matchMapperWeb->load();
        while (!$this->match) {
            $this->match = $matchMapperWeb->load();
        }
    }

    public function testGetSlot()
    {
        $slot = $this->match->getSlot(0);

        $this->assertEquals(68, $slot->get('hero_id'));
        $this->assertEquals(15, $slot->get('level'));
        $this->assertEquals(2, $slot->get('kills'));
        $this->assertEquals(2, $slot->get('deaths'));
        $this->assertEquals(13, $slot->get('assists'));
        $this->assertEquals(49, $slot->get('last_hits'));
        $this->assertEquals(3, $slot->get('denies'));
        $this->assertEquals(264, $slot->get('gold_per_min'));
        $this->assertEquals(337, $slot->get('xp_per_min'));
        $this->assertEquals(501, $slot->get('tower_damage'));
        $this->assertEquals(4527, $slot->get('hero_damage'));
        $this->assertEquals(569, $slot->get('hero_healing'));
        $this->assertEquals(214, $slot->get('item_0'));
        $this->assertEquals(254, $slot->get('item_1'));
        $this->assertEquals(92, $slot->get('item_2'));
        $this->assertEquals(23, $slot->get('item_3'));
        $this->assertEquals(0, $slot->get('item_4'));
        $this->assertEquals(36, $slot->get('item_5'));
    }

    public function testGetAllSlotsDivided()
    {
        $slots = $this->match->getAllSlotsDivided();

        /* @var $slots Dota2Api\Models\Slot[][] */

        $this->assertEquals(5, count($slots['radiant']));
        $this->assertEquals(5, count($slots['dire']));

        $this->assertEquals(86727555, $slots['radiant'][0]->get('account_id'));
        $this->assertEquals(111620041, $slots['radiant'][1]->get('account_id'));
        $this->assertEquals(87276347, $slots['radiant'][2]->get('account_id'));
        $this->assertEquals(40547474, $slots['radiant'][3]->get('account_id'));
        $this->assertEquals(87177591, $slots['radiant'][4]->get('account_id'));

        $this->assertEquals(140153524, $slots['dire'][0]->get('account_id'));
        $this->assertEquals(131237305, $slots['dire'][1]->get('account_id'));
        $this->assertEquals(142750189, $slots['dire'][2]->get('account_id'));
        $this->assertEquals(101375717, $slots['dire'][3]->get('account_id'));
        $this->assertEquals(130416036, $slots['dire'][4]->get('account_id'));
    }

    public function testGetAllPicksBansDivided()
    {
        $picksBans = $this->match->getAllPicksBansDivided();
        $expectedDividedPicksBans = array(
                'radiant' => array(
                    'bans' => array(
                        array(
                            'is_pick' => '0',
                            'hero_id' => '62',
                            'team' => '0',
                            'order' => '0',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '100',
                            'team' => '0',
                            'order' => '2',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '106',
                            'team' => '0',
                            'order' => '8',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '92',
                            'team' => '0',
                            'order' => '10',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '50',
                            'team' => '0',
                            'order' => '17',
                        ),
                    ),
                    'picks' => array(
                        array(
                            'is_pick' => '1',
                            'hero_id' => '72',
                            'team' => '0',
                            'order' => '4',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '89',
                            'team' => '0',
                            'order' => '7',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '17',
                            'team' => '0',
                            'order' => '13',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '7',
                            'team' => '0',
                            'order' => '15',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '68',
                            'team' => '0',
                            'order' => '19',
                        ),
                    ),
                ),
                'dire' => array(
                    'bans' => array(
                        array(
                            'is_pick' => '0',
                            'hero_id' => '52',
                            'team' => '1',
                            'order' => '1',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '105',
                            'team' => '1',
                            'order' => '3',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '55',
                            'team' => '1',
                            'order' => '9',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '11',
                            'team' => '1',
                            'order' => '11',
                        ),
                        array(
                            'is_pick' => '0',
                            'hero_id' => '5',
                            'team' => '1',
                            'order' => '16',
                        ),
                    ),
                    'picks' => array(
                        array(
                            'is_pick' => '1',
                            'hero_id' => '51',
                            'team' => '1',
                            'order' => '5',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '25',
                            'team' => '1',
                            'order' => '6',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '112',
                            'team' => '1',
                            'order' => '12',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '12',
                            'team' => '1',
                            'order' => '14',
                        ),
                        array(
                            'is_pick' => '1',
                            'hero_id' => '49',
                            'team' => '1',
                            'order' => '18',
                        ),
                    ),
                ),
            );
        $this->assertEquals($expectedDividedPicksBans, $picksBans);
    }

}