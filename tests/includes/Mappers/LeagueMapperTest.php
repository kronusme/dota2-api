<?php

use Dota2Api\Mappers\LeagueMapper;

class LeagueMapperTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $mapper = new LeagueMapper();
        $matches = $mapper->load();
        if (count($matches) === 0) {
            $this->markTestSkipped('There are no matches currently');
        } else {
            $match = array_pop($matches);
            $slots = $match->getAllSlots();
            $this->assertCount($slots, 10);
            $this->assertInstanceOf('Dota2Api\Models\LiveMatch', $match);
            $slot_ids = array();
            $needed_slot_ids = array(0, 1, 2, 3, 4, 128, 129, 130, 131, 132);
            foreach ($slots as $slot) {
                $this->assertInstanceOf('Dota2Api\Models\LiveSlot', $slot);
                array_push($slot->get('player_slot'), $slot_ids);
            }
            array_sort($slot_ids);
            $this->assertArraySubset($slot_ids, $needed_slot_ids);
            $this->assertArraySubset($needed_slot_ids, $slot_ids);
            $picks_bans = $match->getAllPicksBans();
            if (count($picks_bans) > 0) {
                $this->assertCount($picks_bans, 20);
            }
        }
    }
}
