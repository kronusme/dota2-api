<?php

use Dota2Api\Models\Match;
use Dota2Api\Mappers\MatchMapperWeb;

class MatchTest extends PHPUnit_Framework_TestCase
{

    public $matchId = 985780481;

    /**
     * @var Match
     */
    public $match;

    public function setUp()
    {
        $matchMapperWeb = new MatchMapperWeb($this->matchId);
        $this->match = $matchMapperWeb->load();
    }

    public function testGetSlot()
    {
        $slot = $this->match->getSlot(0);

        $this->assertEquals(30, $slot->get('hero_id'));
        $this->assertEquals(21, $slot->get('level'));
        $this->assertEquals(9, $slot->get('kills'));
        $this->assertEquals(8, $slot->get('deaths'));
        $this->assertEquals(14, $slot->get('assists'));
        $this->assertEquals(97, $slot->get('last_hits'));
        $this->assertEquals(0, $slot->get('denies'));
        $this->assertEquals(310, $slot->get('gold_per_min'));
        $this->assertEquals(393, $slot->get('xp_per_min'));
        $this->assertEquals(1090, $slot->get('tower_damage'));
        $this->assertEquals(6978, $slot->get('hero_damage'));
        $this->assertEquals(4280, $slot->get('hero_healing'));
        $this->assertEquals(180, $slot->get('item_0'));
        $this->assertEquals(37, $slot->get('item_1'));
        $this->assertEquals(108, $slot->get('item_2'));
        $this->assertEquals(42, $slot->get('item_3'));
        $this->assertEquals(81, $slot->get('item_4'));
        $this->assertEquals(36, $slot->get('item_5'));
    }

    public function testGetAllSlotsDivided()
    {
        $slots = $this->match->getAllSlotsDivided();

        /* @var $slots Dota2Api\Models\Slot[][] */

        $this->assertEquals(5, count($slots['radiant']));
        $this->assertEquals(5, count($slots['dire']));

        $this->assertEquals(86727555, $slots['radiant'][0]->get('account_id'));
        $this->assertEquals(87177591, $slots['radiant'][1]->get('account_id'));
        $this->assertEquals(87276347, $slots['radiant'][2]->get('account_id'));
        $this->assertEquals(73562326, $slots['radiant'][3]->get('account_id'));
        $this->assertEquals(110880087, $slots['radiant'][4]->get('account_id'));

        $this->assertEquals(87278757, $slots['dire'][0]->get('account_id'));
        $this->assertEquals(19672354, $slots['dire'][1]->get('account_id'));
        $this->assertEquals(41231571, $slots['dire'][2]->get('account_id'));
        $this->assertEquals(82262664, $slots['dire'][3]->get('account_id'));
        $this->assertEquals(94155156, $slots['dire'][4]->get('account_id'));
    }

    public function testGetAllPicksBansDivided()
    {
        $picksBans = $this->match->getAllPicksBansDivided();
        $expectedPicksBans = array(
            'radiant' => array(
                'bans' => array(

                    array(
                        'is_pick' => '0',
                        'hero_id' => '91',
                        'team' => '0',
                        'order' => '1'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '103',
                        'team' => '0',
                        'order' => '3'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '33',
                        'team' => '0',
                        'order' => '9'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '1',
                        'team' => '0',
                        'order' => '11'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '20',
                        'team' => '0',
                        'order' => '16'
                    )
                ),
                'picks' => array(

                    array(
                        'is_pick' => '1',
                        'hero_id' => '29',
                        'team' => '0',
                        'order' => '5'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '77',
                        'team' => '0',
                        'order' => '6'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '30',
                        'team' => '0',
                        'order' => '12'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '13',
                        'team' => '0',
                        'order' => '14'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '88',
                        'team' => '0',
                        'order' => '18'
                    )
                )
            ),
            'dire' => array(
                'bans' => array(

                    array(
                        'is_pick' => '0',
                        'hero_id' => '15',
                        'team' => '1',
                        'order' => '0'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '64',
                        'team' => '1',
                        'order' => '2'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '92',
                        'team' => '1',
                        'order' => '8'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '65',
                        'team' => '1',
                        'order' => '10'
                    ),
                    array(
                        'is_pick' => '0',
                        'hero_id' => '83',
                        'team' => '1',
                        'order' => '17'
                    )
                ),
                'picks' => array(

                    array(
                        'is_pick' => '1',
                        'hero_id' => '78',
                        'team' => '1',
                        'order' => '4'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '7',
                        'team' => '1',
                        'order' => '7'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '69',
                        'team' => '1',
                        'order' => '13'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '94',
                        'team' => '1',
                        'order' => '15'
                    ),
                    array(
                        'is_pick' => '1',
                        'hero_id' => '90',
                        'team' => '1',
                        'order' => '19'
                    )
                )
            )
        );
        $this->assertEquals($expectedPicksBans, $picksBans);
    }

}