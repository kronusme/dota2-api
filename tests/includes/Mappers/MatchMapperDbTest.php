<?php

use Dota2Api\Utils\Db;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Mappers\LeaguesMapperDb;
use Dota2Api\Mappers\MatchMapperDb;
use Dota2Api\Mappers\MatchMapperWeb;
use Dota2Api\Models\Player;
use Dota2Api\Models\Match;

class MatchMapperDbTest extends PHPUnit_Framework_TestCase
{

    public $match_id = '683300315';

    public static function setUpBeforeClass() {
        $leagues_mapper_web = new LeaguesMapperWeb();
        $leagues = $leagues_mapper_web->load();
        $leagues_mapper_db = new LeaguesMapperDb();
        $leagues_mapper_db->save($leagues[600]);
    }

    public static function tearDownBeforeClass() {
        $db = Db::obtain();
        $db->exec('DELETE FROM picks_bans');
        $db->exec('DELETE FROM ability_upgrades');
        $db->exec('DELETE FROM additional_units');
        $db->exec('DELETE FROM slots');
        $db->exec('DELETE FROM matches');
        $db->exec('DELETE FROM leagues');
    }

    public function testLoad() {

        $expected_match_info = array(
            'game_mode' => '2',
            'radiant_win' => '1',
            'first_blood_time' => '7',
            'leagueid' => '600',
            'duration' => '1662',
        );

        $mapper_web = new MatchMapperWeb($this->match_id);
        $match = $mapper_web->load();
        $mapper_db = new MatchMapperDb();
        $mapper_db->save($match);
        $match = $mapper_db->load($this->match_id);

        $this->assertInstanceOf('Dota2Api\Models\Match', $match);
        foreach($expected_match_info as $k=>$v) {
            $this->assertEquals($v, $match->get($k));
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
            $this->assertTrue($slots[$slot_id]->get('account_id') !== Player::ANONYMOUS);
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

    public function testUpdate() {
        $mapper_db = new MatchMapperDb();
        $match = $mapper_db->load($this->match_id);
        $match->set('first_blood_time', 0);
        $slots = $match->get_all_slots();
        $slots[0]->set('hero_id', '1');
        $match->set_all_slots($slots);
        $mapper_db->update($match, false);

        $match = $mapper_db->load($this->match_id);

        $this->assertEquals(0, $match->get('first_blood_time'));
        $slots = $match->get_all_slots();
        $this->assertEquals(1, $slots[0]->get('hero_id'));

    }

    public function testDelete() {
        $mapper_db = new MatchMapperDb();
        $match = $mapper_db->load($this->match_id);
        $mapper_db->save($match);

        $mapper_db->delete($match->get('match_id'));

        $match = $mapper_db->load($this->match_id);
        $this->assertNull($match->get('match_id'));
    }

}
