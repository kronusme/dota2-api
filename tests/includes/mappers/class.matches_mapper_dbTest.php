<?php

class matches_mapper_dbTest extends PHPUnit_Framework_TestCase
{
    protected $match_id = 985780481;

    protected $league_id = 1803;

    public static function setUpBeforeClass() {
        $db = db::obtain();
        $db->exec('DELETE FROM picks_bans');
        $db->exec('DELETE FROM ability_upgrades');
        $db->exec('DELETE FROM additional_units');
        $db->exec('DELETE FROM slots');
        $db->exec('DELETE FROM matches');
    }

    public function setUp() {

        $leagues_mapper_web = new leagues_mapper_web();
        $leagues = $leagues_mapper_web->load();
        $leagues_mapper_db = new leagues_mapper_db();
        $leagues_mapper_db->save($leagues[$this->league_id]);

        $match_mapper_web = new match_mapper_web($this->match_id);
        $match = $match_mapper_web->load();
        $match_mapper_db = new match_mapper_db();
        $match_mapper_db->save($match);
    }

    public function testLoad() {

        $matches_mapper_db = new matches_mapper_db();
        $matches_mapper_db->set_league_id($this->league_id)->set_matches_requested(1);
        $matches = $matches_mapper_db->load();
        $match = array_pop($matches);

        $this->assertEquals($match->get('match_id'), $this->match_id);
        $this->assertEquals($match->get('game_mode'), 2);
        $this->assertEquals($match->get('tower_status_radiant'), 1796);
        $this->assertEquals($match->get('tower_status_dire'), 256);
        $this->assertEquals($match->get('radiant_win'), 1);
        $this->assertEquals($match->get('duration'), 3526);
        $this->assertEquals($match->get('first_blood_time'), 47);
        $this->assertStringStartsWith('2014-10-26', $match->get('start_time'));
        $this->assertEquals($match->get('barracks_status_radiant'), 55);
        $this->assertEquals($match->get('barracks_status_dire'), 50);
        $this->assertEquals($match->get('lobby_type'), 1);
        $this->assertEquals($match->get('human_players'), 10);
        $this->assertEquals($match->get('leagueid'), 1803);
        $this->assertEquals($match->get('cluster'), 136);
        $this->assertEquals($match->get('radiant_name'), 'Evil Geniuses');
        $this->assertEquals($match->get('radiant_team_id'), 39);
        $this->assertEquals($match->get('dire_name'), 'Team is Secret');
        $this->assertEquals($match->get('dire_team_id'), 1838315);

        $slots = $match->get_all_slots();

        $this->assertEquals(count($slots), 10);
        $slot = $slots[0];
        $this->assertEquals($slot->get('match_id'), $this->match_id);
        $this->assertEquals($slot->get('account_id'), 86727555);
        $this->assertEquals($slot->get('hero_id'), 30);
        $this->assertEquals($slot->get('player_slot'), 0);
        $this->assertEquals($slot->get('item_0'), 180);
        $this->assertEquals($slot->get('item_1'), 37);
        $this->assertEquals($slot->get('item_2'), 108);
        $this->assertEquals($slot->get('item_3'), 42);
        $this->assertEquals($slot->get('item_4'), 81);
        $this->assertEquals($slot->get('item_5'), 36);
        $this->assertEquals($slot->get('kills'), 9);
        $this->assertEquals($slot->get('deaths'), 8);
        $this->assertEquals($slot->get('assists'), 14);
        $this->assertEquals($slot->get('leaver_status'), 0);
        $this->assertEquals($slot->get('gold'), 3883);
        $this->assertEquals($slot->get('last_hits'), 97);
        $this->assertEquals($slot->get('denies'), 0);
        $this->assertEquals($slot->get('gold_per_min'), 310);
        $this->assertEquals($slot->get('xp_per_min'), 393);
        $this->assertEquals($slot->get('gold_spent'), 14470);
        $this->assertEquals($slot->get('hero_damage'), 6978);
        $this->assertEquals($slot->get('tower_damage'), 1090);
        $this->assertEquals($slot->get('hero_healing'), 4280);
        $this->assertEquals($slot->get('level'), 21);

        $get_all_picks_bans = $match->get_all_picks_bans();

        $this->assertEquals(count($get_all_picks_bans), 20);

    }

    public function testDelete() {

        $additional_match_id = 886357301;
        $match_mapper_web = new match_mapper_web($additional_match_id);
        $match = $match_mapper_web->load();
        $match_mapper_db = new match_mapper_db();
        $match_mapper_db->save($match);

        $matches_mapper_db = new matches_mapper_db();
        $matches_mapper_db->delete(array($additional_match_id, $this->match_id));

        $db = db::obtain();
        $this->assertEquals(0, count($db->fetch_array_pdo('SELECT * FROM matches')));
        $this->assertEquals(0, count($db->fetch_array_pdo('SELECT * FROM slots')));
        $this->assertEquals(0, count($db->fetch_array_pdo('SELECT * FROM additional_units')));
        $this->assertEquals(0, count($db->fetch_array_pdo('SELECT * FROM ability_upgrades')));
        $this->assertEquals(0, count($db->fetch_array_pdo('SELECT * FROM picks_bans')));

    }

}
